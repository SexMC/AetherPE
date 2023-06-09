<?php

declare(strict_types=1);

namespace skyblock\utils;

use czechpmdevs\multiworld\generator\void\VoidGenerator;
use Generator;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use pocketmine\world\WorldCreationOptions;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\island\IslandSetUnloadingPacket;
use skyblock\Database;
use skyblock\islands\Island;
use skyblock\islands\IslandHandler;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\SkyblockMenuItem;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\profile\Profile;
use skyblock\sessions\Session;
use skyblock\tasks\FileCopyAsyncTask;
use skyblock\tasks\FileDecompressTask;
use skyblock\tasks\FileDeleteTask;
use SOFe\AwaitGenerator\Await;
use Throwable;
use Webmozart\PathUtil\Path;

class ProfileUtils {


	public static function switchProfile(AetherPlayer $player, Profile $profile): void {
		Await::f2c(function() use($player, $profile) {
			(new Session($player))->setLastProfileSwitchUnix(time());

			yield Main::getInstance()->getStd()->sleep(60);

			if($player->isOnline()){


				$previous = $player->getCurrentProfilePlayerSession();
				$previous->saveEverything($player);


				$player->getInventory()->clearAll();
				$player->getEnderInventory()->clearAll();
				$player->getArmorInventory()->clearAll();


				$player->setSelectedProfileId($profile->getUniqueId(), Utils::isHubServer());

				if(!Utils::isHubServer()){
					Utils::hub($player);
				} else {
					$new = $player->getCurrentProfilePlayerSession();
					$player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
					$player->getInventory()->setContents($new->getInventory());
					$player->getEnderInventory()->setContents($new->getEnderchestInventory());
					$player->getArmorInventory()->setContents($new->getArmorInventory());
					$player->getXpManager()->setCurrentTotalXp($new->getMinecraftXP());

					$player->getInventory()->setItem(8, SkyblockItems::SKYBLOCK_MENU_ITEM());
				}
			}
		});
	}

	public static function createNewProfile(string $owner, array $coops): Profile {
		$p = Profile::new($owner, $coops);
		$id = $p->getUniqueId();


		$options = new WorldCreationOptions();
		$options->setGeneratorClass(VoidGenerator::class);
		$options->setSpawnPosition(new Vector3(271, 108, 280));
		$manager = Server::getInstance()->getWorldManager();

		$manager->generateWorld("profile-$id", $options, false);

		Await::f2c(function() use($id, $manager, $p){
			$server = Server::getInstance();
			yield Main::getInstance()->getStd()->sleep(5);
			$manager->unloadWorld($manager->getWorldByName("profile-$id"));
			yield Main::getInstance()->getStd()->sleep(5);


			$server->getAsyncPool()->submitTask(new FileDeleteTask(Path::join(IslandHandler::getWorldDirectory($id), "db"), yield Await::RESOLVE));
			yield Await::ONCE;

			$server->getAsyncPool()->submitTask(new FileCopyAsyncTask(Path::join(Main::getInstance()->getDataFolder(), "island"), Path::join(self::getWorldDirectory($id), "db"), yield Await::RESOLVE));
			yield Await::ONCE;

			$data = yield self::saveProfile($p);

			if($data === true){
				Main::debug("Successfully created profile world $id");
			} else Main::debug("Error happened while saving profile: $id");
		});

		return $p;
	}

	public static function saveProfilesSynchronously(): void {
		$manager = Server::getInstance()->getWorldManager();
		foreach(self::getLoadedProfiles() as $is){
			$island = new Profile($is);

			Main::debug("force saving profile {$island->getUniqueId()}");

			$manager->unloadWorld($island->getWorld());

			Filesystem::recursiveCopy($island->getFolderOnServer(), $island->getFolderOnMount());
			FileDeleteTask::delete($island->getFolderOnServer());
			Main::debug("force saved profile {$island->getUniqueId()}");
		}
	}

	/**
	 * @param string $profileId
	 *
	 * @return Generator<bool>
	 */
	public static function loadProfile(string $profileId): Generator {
		/*this part of the code is normally for backwards compatibility, not needed for new ssn
		 * $data = yield Database::getInstance()->getLibasynql()->asyncSelect(Queries::WORLDS_GET, ["world" => $profileId]);
		if(isset($data[0])){
			Main::debug("Async selects island world data done");

			$zipPath = self::getWorldDirectory($profileId) . ".zip";
			$outputPath = self::getWorldDirectory($profileId);

			file_put_contents(self::getWorldDirectory($profileId) . ".zip", $data[0]["data"]);

			Main::getInstance()->getServer()->getAsyncPool()->submitTask(new FileDecompressTask($zipPath, $outputPath, yield Await::RESOLVE));
			$result = yield Await::ONCE;
			Main::debug("Island world decompressed");

			if($result === true){
				Main::getInstance()->getServer()->getAsyncPool()->submitTask(new FileDeleteTask($zipPath, yield Await::RESOLVE));
				yield Await::ONCE;
				Server::getInstance()->getWorldManager()->loadWorld("profile-$profileId");
				Main::debug("Deleting island world file");

				yield Database::getInstance()->getLibasynql()->asyncGeneric(Queries::WORLDS_DELETE, ["name" => $profileId]);
				return true;
			}
		}*/

		$island = new Profile($profileId);
		Server::getInstance()->getAsyncPool()->submitTask(new FileCopyAsyncTask($island->getFolderOnMount(), $island->getFolderOnServer(), yield Await::RESOLVE));
		$data = yield Await::ONCE;
		if($data === false) return false;

		$manager = Server::getInstance()->getWorldManager();
		$manager->loadWorld("profile-$profileId");

		return true;
	}

	/**
	 * @param Profile $island
	 * @param bool   $sendUnloadingPacket
	 *
	 * @return Generator<bool>
	 */
	public static function saveProfile(Profile $island, bool $sendUnloadingPacket = true): Generator {
		try{
			$manager = Server::getInstance()->getWorldManager();

			if(($world = $island->getWorld())){
				foreach($world->getPlayers() as $p){
					$p->sendMessage(Main::PREFIX . "This world is being unloaded, you have been teleported to spawn");
					Utils::hub($p);
				}

				$manager->unloadWorld($world);
			}

			if($sendUnloadingPacket) {
				CommunicationLogicHandler::getInstance()->sendPacket(new IslandSetUnloadingPacket($island->getUniqueId(), true));
			}

			if(!is_dir("/islandworlds/profile-{$island->getUniqueId()}")){
				mkdir("/islandworlds/profile-{$island->getUniqueId()}");
				mkdir("/islandworlds/profile-{$island->getUniqueId()}/db");
			}

			Main::debug("Saving profile {$island->getUniqueId()}");
			Server::getInstance()->getAsyncPool()->submitTask(new FileCopyAsyncTask($island->getFolderOnServer(), $island->getFolderOnMount(), yield Await::RESOLVE));
			yield Await::ONCE;
			Server::getInstance()->getAsyncPool()->submitTask(new FileDeleteTask($island->getFolderOnServer(), yield Await::RESOLVE));
			yield Await::ONCE;
			Main::debug("Saved profile {$island->getUniqueId()}");


			if($sendUnloadingPacket){
				CommunicationLogicHandler::getInstance()->sendPacket(new IslandSetUnloadingPacket($island->getUniqueId(), false));
			}

			return true;
		} catch(Throwable $e) {
			Server::getInstance()->getLogger()->logException($e);
		}

		return false;
	}

	/**
	 * @param Server|null $server
	 *
	 * @return string[]
	 */
	public static function getLoadedProfiles(Server $server = null): array {
		$array = [];
		foreach(Server::getInstance()->getWorldManager()->getWorlds() as $world){
			if(str_contains($world->getFolderName(), "profile-") === true){
				$array[] = str_replace("profile-", "", $world->getFolderName());
			}
		}

		return $array;
	}

	public static function getWorldDirectory(string $id): string
	{
		return Path::join(Server::getInstance()->getDataPath(), 'worlds', "profile-" . $id);
	}
}