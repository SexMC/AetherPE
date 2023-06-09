<?php

declare(strict_types=1);

namespace skyblock\utils;

use czechpmdevs\multiworld\generator\void\VoidGenerator;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;
use skyblock\communication\packets\types\island\IslandLoadRequestPacket;
use skyblock\communication\packets\types\island\IslandLoadResponsePacket;
use skyblock\communication\packets\types\island\IslandLocationRequestPacket;
use skyblock\communication\packets\types\island\IslandLocationResponsePacket;
use skyblock\communication\packets\types\player\PlayerTeleportRequestPacket;
use skyblock\communication\packets\types\player\PlayerTeleportResponsePacket;
use skyblock\Database;
use skyblock\islands\Island;
use skyblock\islands\IslandHandler;
use skyblock\islands\IslandInterface;
use skyblock\items\special\types\SpawnerItem;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\tasks\FileCopyAsyncTask;
use skyblock\tasks\FileDeleteTask;
use skyblock\tasks\RestartTask;
use SOFe\AwaitGenerator\Await;
use Webmozart\PathUtil\Path;

class IslandUtils {

	public static function teleportWarp(Player $player, string $island): void {
		$valid = false;

		if(!self::islandExists($island)){
			$session = new Session($island);
			if($session->playerExists()){
				if(($name = $session->getIslandName()) !== null){
					$island = $name;
					$valid = true;
				}
			}
		} else $valid = true;

		if($valid === true){
			$is = new Island($island);
			$warp = $is->getWarp();

			if($warp === null){
				$player->sendMessage(Main::PREFIX . "This island has no warp set");
				return;
			}

			if($is->getSetting(IslandInterface::SETTINGS_LOCKED) === true){
				$player->sendMessage(Main::PREFIX . "This island is locked");
				return;
			}

			Await::f2c(function()  use($player, $island, $warp, $is){
				Main::getInstance()->getCommunicationLogicHandler()->sendPacket(new IslandLocationRequestPacket($island, yield Await::RESOLVE));
				/** @var IslandLocationResponsePacket $pk */
				$pk = yield Await::ONCE;

				if(!$player->isOnline()) return;

				$location = $pk->location;

				if($location === "not_loaded"){
					$player->sendMessage(Main::PREFIX . "You cannot warp to an island while all of its members are offline");
					return;
				}

				if($location === Utils::getServerName()){
					$player->teleport(Position::fromObject($warp, $is->getWorld()));
					return;
				}

				Main::getInstance()->getCommunicationLogicHandler()->sendPacket(new PlayerTeleportRequestPacket($player->getName(), PlayerTeleportRequestPacket::MODE_ISLAND_WARP, $is->getName(), Utils::getServerName(), yield Await::RESOLVE));
				/** @var PlayerTeleportResponsePacket $pk */
				$pk = yield Await::ONCE;
				if(!$player->isOnline()) return;

				if($pk->server !== "error"){
					Utils::transfer($player, $pk->server);
				} else $player->sendMessage(Main::PREFIX . "An error occurred while trying to warp.");
			});
		} else $player->sendMessage(Main::PREFIX . "No island or player named \"{$island}\" was found.");
	}

	public static function createIsland(Player $player, Session $session, string $name): bool {
		$redis = Database::getInstance()->getRedis();
		$server = Server::getInstance();
		$realName = $name;
		$name = strtolower($name);

		$options = new WorldCreationOptions();
		$options->setGeneratorClass(VoidGenerator::class);
		$options->setSpawnPosition(new Vector3(265, 55, 265));
		$manager = Server::getInstance()->getWorldManager();

		$create = $manager->generateWorld("is-$name", $options, false);

		if($create === true){
			//in island create form add checks if island name is used
			$session->setIslandName($name);
			$redis->set("island.$name.leader", $player->getName());
			$redis->set("island.$name.realname", $realName);
			$redis->set("island.$name.created", time());
			Await::f2c(function() use($name, $player, $session, $server, $manager){
				yield Main::getInstance()->getStd()->sleep(15);
				$manager->unloadWorld($manager->getWorldByName("is-$name"));
				yield Main::getInstance()->getStd()->sleep(15);


				$server->getAsyncPool()->submitTask(new FileDeleteTask(Path::join(IslandHandler::getWorldDirectory($name), "db"), yield Await::RESOLVE));
				yield Await::ONCE;

				$server->getAsyncPool()->submitTask(new FileCopyAsyncTask(Path::join(Main::getInstance()->getDataFolder(), "island"), Path::join(IslandHandler::getWorldDirectory($name), "db"), yield Await::RESOLVE));
				yield Await::ONCE;

				$data = yield IslandHandler::getInstance()->saveIsland(new Island($name));

				if($data === true){
					yield Main::getInstance()->getStd()->sleep(10);
					IslandUtils::go($player, $session);
				} else Main::debug("Error happened while saving island: $name");
			});
		} else $player->sendMessage(Main::PREFIX . "Error happened while trying to create the island");

		return true;
	}

	public static function kickMember(Island $island, Player $kicker, string $kicked): void {
		$island->removeMember($kicked);

		if(strtolower($island->getLeader()) === strtolower($kicked)){
			return;
		}
		$session = new Session($kicked);
		$session->setIslandName(null);
		Utils::sendMessage($kicked, Main::PREFIX . "§cYou got kicked from your island");


		Main::debug($kicker->getName() . " kicked $kicked from island id: " . $island->getName());
		$island->announce(Main::PREFIX . "§c{$kicker->getName()}§7 has kicked §c$kicked §7from the island");
	}

	public static function go(Player $player, Session $session, string $islandName = null): bool {
		$island = $islandName ?? $session->getIslandName();

		if($island === null) return false;

		$player->sendMessage(Main::PREFIX . "§7Loading your island..");
		if(($world = Server::getInstance()->getWorldManager()->getWorldByName("is-" . $island))){
			$player->teleport($world->getSpawnLocation());
			return true;
		}

		Main::getInstance()->getCommunicationLogicHandler()->sendPacket(new IslandLoadRequestPacket($island, Utils::getServerName(), function(IslandLoadResponsePacket $pk) use($player) : void{
			if(!$player->isOnline()) return;

			$server = $pk->loadedServer;

			if($server === "error"){
				$player->sendMessage(Main::PREFIX . "Error occurred while loading your island");
				return;
			}

			if($server === "not_available"){
				$player->sendMessage(Main::PREFIX . "No available island servers were found.");
				return;
			}

			if($server === "loading"){
				$player->sendMessage(Main::PREFIX . "Your island is already being loaded, please try again in 5 seconds");
				return;
			}

			if($server === "unloading"){
				$player->sendMessage(Main::PREFIX . "Your island is being unloaded, please try again in 5 seconds");
				return;
			}

			Utils::transfer($player, $server);
		}));

		return true;
	}

	public static function isIslandWorld(World|string $world): bool {
		return str_contains(($world instanceof World ? $world->getDisplayName() : $world), "is-");
	}

	public static function getIslandByWorld(World $world): Island {
		return new Island(str_replace("is-", "", $world->getDisplayName()));
	}

	public static function islandExists(string $island): bool {
		return (new Island($island))->exists();
	}

	/**
	 * @param Server|null $server
	 *
	 * @return string[]
	 */
	public static function getLoadedIslands(Server $server = null): array {
		$array = [];
		foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
			if(str_contains($world->getFolderName(), "is-") === true){
				$array[] = str_replace("is-", "", $world->getFolderName());
			}
		}

		return $array;
	}

	public static function getNearbyEnemies(Player $player, int $radius = 6): array {
		$session = new Session($player);

		$island = (($name = $session->getIslandName()) === null ? null : new Island($name));

		$enemies = [];
		foreach (EntityUtils::getNearbyEntitiesFromPosition($player->getPosition(), $radius) as $entity) {
			if ($entity instanceof Player && ($island === null || $island->isMember($entity->getName()))) {
				$enemies[] = $entity;
			}
		}

		return $enemies;
	}

	public static function getValueBySpawner(string $spawner): int {
		return match ($spawner) { //shop prices divided by 2
			default => 0,

			EntityIds::CHICKEN => 23500 / 2,
			EntityIds::COW => 36500 / 2,
			EntityIds::ZOMBIE => 80000 / 2,
			EntityIds::SKELETON => 90000 / 2,
			EntityIds::BLAZE => 360000 / 2,
			EntityIds::SLIME => 560000 / 2,
			EntityIds::IRON_GOLEM => 725000 / 2,
			EntityIds::ZOMBIE_PIGMAN => 1250000 / 2,
			EntityIds::MAGMA_CUBE => 1750000 / 2,
			EntityIds::GUARDIAN => 2325000 / 2,
			EntityIds::TURTLE => 3325000 / 2,

			SpawnerItem::PIGLIN_BRUTE => 2000000,
			EntityIds::ZOGLIN => 2500000,
			EntityIds::RAVAGER => 3000000,
			SpawnerItem::WARDEN => 3500000,
		};
	}
}