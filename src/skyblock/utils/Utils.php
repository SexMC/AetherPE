<?php

declare(strict_types=1);

namespace skyblock\utils;

use Closure;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\TransferPacket;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\TaskHandler;
use pocketmine\Server;
use skyblock\caches\combat\CombatCache;
use skyblock\caches\pvpzones\PvpZonesCache;
use skyblock\communication\CommunicationData;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\player\PlayerTeleportRequestPacket;
use skyblock\communication\packets\types\player\PlayerTeleportResponsePacket;
use skyblock\communication\packets\types\server\ServerMessagePacket;
use skyblock\items\ItemEditor;
use skyblock\items\misc\PersonalCompactor4000;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\PersonalCompactorItem;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\logs\LogHandler;
use skyblock\logs\types\DupeLog;
use skyblock\Main;
use skyblock\misc\collection\Collection;
use skyblock\misc\collection\CollectionHandler;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\player\AetherPlayer;
use skyblock\tasks\DelayedRepeatingTemporaryTask;
use skyblock\tasks\ImportantTask;
use SOFe\AwaitGenerator\Await;

class Utils {

	/** @var TaskHandler[] */
	public static array $importantTasks = [];

	public static string $serverType;
	public static string $serverName;
	public static bool $isDev;

	private static array $colors = ["§2", "§3", "§4", "§5", "§6", "§7", "§8", "§d", "§b", "§e", "§a"];

	public static function isIslandServer(): bool {
		return self::$serverType === "island";
	}

	public static function isHubServer(): bool {
		return self::$serverType === "hub";
	}

	public static function isWarpServer(): bool {
		return self::$serverType === "warp";
	}

	public static function getServerName() : string{
		return self::$serverName;
	}

	public static function hub(Player $player): void {
		self::transfer($player, "hub", 0);
	}

	public static function transfer(AetherPlayer $player, string $server, int $port = 0, bool $checkCombat = true): void {
		if(!$player->isAlive()) {
			$player->sendMessage(Main::PREFIX . "You tried to switch servers while you didn't have any health");
			return;
		}


		if($checkCombat === true){
			if (PvpZonesCache::getInstance()->isPvpEnabled($player->getPosition()) || str_contains($player->getWorld()->getDisplayName(), "a-") || strtolower($player->getWorld()->getDisplayName()) === "nether") {
				$player->sendMessage(Main::PREFIX . "You cannot switch servers while being in PvP/PvE zones. Please go to /spawn.");
				return;
			}


			if (CombatCache::getInstance()->isInCombat($player)) {
				$player->sendMessage(Main::PREFIX . "You tried to switch servers while in combat");
				return;
			}
		}

		$player->getCurrentProfilePlayerSession()->saveEverything($player);

		$transferpk = new TransferPacket();
		$transferpk->address = $server;
		$transferpk->port = 0;

		$player->getNetworkSession()->sendDataPacket($transferpk, true);
	}

	public static function message(string $player, string $message): void {
		CommunicationLogicHandler::getInstance()->sendPacket(new ServerMessagePacket($message, [$player]));
	}

    public static function teleport(Player $player, string $username): void {
        if (($target = Server::getInstance()->getPlayerExact($username)) instanceof Player) {
            $player->teleport($target->getPosition());
            return;
        }

        Await::f2c(function() use (&$player, $username){
			Main::getInstance()->getCommunicationLogicHandler()->sendPacket(new PlayerTeleportRequestPacket(
				$player->getName(),
				PlayerTeleportRequestPacket::MODE_PLAYER,
				$username,
				Utils::getServerName(),
				yield Await::RESOLVE
			));

			/** @var PlayerTeleportResponsePacket $data */
			$data = yield Await::ONCE;

			if($data->server === "error"){
				$player->sendMessage(Main::PREFIX . "An error has occurred please retry");
				return;
			}

			if($data->server === Utils::getServerName()){
				if(($p = Server::getInstance()->getPlayerExact($username))){
					$player->teleport($p->getLocation());
					return;
				}

				$player->sendMessage(Main::PREFIX . "An error has occurred please retry");
				return;
			}

			Utils::transfer($player, $data->server);
        });
    }

	public static function addItem(Player $player, Item $item, bool $drop = true, bool $addCollection = false): void {
		//TODO: add collection
		//TODO: agronomoy sack checks

		assert($player instanceof AetherPlayer);

		if($addCollection){
			$s = $player->getCurrentProfile()->getProfileSession();
			$c = CollectionHandler::getInstance()->collectionByItems[$item->getId() . $item->getMeta()] ?? null;
			if($c instanceof Collection){
				$s->increaseCollectionCount($c->getName(), $item->getCount());

				$lvl = $s->getCollectionLevel($c->getName());
				if($s->getCollectionCount($c->getName()) >= $c::getNeededForLevel($lvl+1)){
					$c->levelUp($player);
				}
			}


			//storage sack logic start
			for($i = 0; $i <= 8; $i++){
				if($item->getCount() <= 0) return;

				$invItem = $player->getInventory()->getItem($i);
				if($item->isNull()) continue;

				$special = SpecialItem::getSpecialItem($invItem);
				if($invItem instanceof StorageSack){
					$tag = $invItem->getNamedTag()->getCompoundTag("storage_items");
					$count = $tag->getInt($item->getId() . ":" . $item->getMeta(), -38);
					//sack does not have that item as storage option
					if($count === -38) continue;

					$capacity = $invItem->getCapacity();

					$canAdd = $capacity - $count;

					if($canAdd >= $item->getCount()){
						$tag->setInt($item->getId() . ":" . $item->getMeta(), $count + $item->getCount());
						$item->setCount(0);
					} else {
						$tag->setInt($item->getId() . ":" . $item->getMeta(), $count + $canAdd);
						$item->setCount($item->getCount() - $canAdd);
					}

					$player->getInventory()->setItem($i, $invItem);
					continue;
				}

				if($invItem instanceof PersonalCompactor4000){
					$it = $invItem->getSelectedItem();

					if($it === null) continue;

					$recipe = RecipesHandler::getInstance()->getRecipeByItem($it);

					if($recipe === null) continue;


					$arr = $recipe->getInput();
					$itemNeeded = array_shift($arr);

					if(!$itemNeeded->equals($item)) continue;

					$countNeeded = $itemNeeded->getCount();
					$foundCount = $item->getCount();

					$foundArray = [];
					for($i = 0; $i <= 35; $i++){
						$x = $player->getInventory()->getItem($i);
						if(ItemEditor::isGlowing($x)) continue;

						if($x->equals($itemNeeded)){
							$foundCount += $x->getCount();

							$foundArray[$i] = $x;
						}
					}






					if($foundCount >= $countNeeded){
						while(true){
							$deleted = 0;
							//NOTE: needed custom removing logic as Inventory->removeItem() wasn't working as expected. It was removing the normal items but also removing the enchanted items as well
							foreach($foundArray as $key => $index){
								$indexCount = $index->getCount();

								if($deleted >= $countNeeded) break;


								if($deleted + $indexCount <= $countNeeded){
									$deleted += $indexCount;
									$player->getInventory()->clear($key);
									unset($foundArray[$key]);
								} else {
									$index->setCount($index->getCount() - ($countNeeded - $deleted));
									$player->getInventory()->setItem($key, $index);

									$deleted = $countNeeded;
								}
							}

							if($deleted < $countNeeded) break;
							$foundCount -= $deleted;

							$output = clone $recipe->getOutput();
							$output->setCount(1);
							$player->getInventory()->addItem($output);

							if($foundCount < $countNeeded){
								break;
							}
						}
					}
				}


			}
			//storage sack logic end
		}

		if($item->getCount() <= 0) return;





		if ($drop === true) {
			foreach ($player->getInventory()->addItem($item) as $i) {
				$player->getWorld()->dropItem($player->getPosition()->asVector3(), $i);
			}
		} else $player->getInventory()->addItem($item);
	}

	public static function getOnlinePlayerUsernames(): array {
		return CommunicationData::getOnlinePlayers();
	}

	public static function getOnlinePlayerUsernamesLocally(): array {
		$v = [];
		foreach(Server::getInstance()->getOnlinePlayers() as $p){
			$v[] = strtolower($p->getName());
		}


		return $v;
	}

	public static function getOnlinePlayerObjectsLocally(): array {
		$v = [];
		foreach(Server::getInstance()->getOnlinePlayers() as $p){
			$v[strtolower($p->getName())] = $p;
		}

		return $v;
	}

	public static function isOnline(string $online): bool {
		return CommunicationData::isOnline(strtolower($online));
	}

	public static function sendMessage(string|array $usernames, string $message): void {
		if(!is_array($usernames)){
			$usernames = [$usernames];
		}

		CommunicationLogicHandler::getInstance()->sendPacket(new ServerMessagePacket($message, $usernames));
	}

	public static function announce(string|array $message): void {
		if(is_array($message)) {
			$message = implode("\n", $message);
		}

		CommunicationLogicHandler::getInstance()->sendPacket(new ServerMessagePacket($message, []));
	}

	public static function executeLater(Closure $closure, int $delay, bool $important = false): TaskHandler {
		if (!$important) {
			$handler = Main::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($closure):  void {
				($closure)();
			}), $delay);
		} else {
			$task = new ImportantTask($closure, $id = uniqid("id"));
			$handler = Main::getInstance()->getScheduler()->scheduleDelayedTask($task, $delay);
			Utils::$importantTasks[$id] = $handler;
		}

		return $handler;
	}

	public static function executeRepeatedly(Closure $closure, int $repeatDelay, int $scheduleDelay = 0): TaskHandler {
		return Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function () use ($closure): void {
			($closure)();
		}), $scheduleDelay, $repeatDelay);
	}

	public static function executeRepeatedlyFor(Closure $closure, int $repeatDelay, int $scheduleDelay = 0, int $repeatFor = 1): TaskHandler {
		return Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new DelayedRepeatingTemporaryTask($repeatFor, $closure), $scheduleDelay, $repeatDelay);
	}

	public static function checkForAHDupedItems(Player $player): bool {
		$removed = [];

		/**
		 * @var int $index
		 * @var Item $item
		 */
		foreach($player->getInventory()->getContents() as $index => $item){
			if($item->getNamedTag()->getString("auctionID", "") !== "" || $item->getNamedTag()->getString("menuItem", "") !== ""){
				$removed[] = $item;
				$player->getInventory()->clear($index);
			}
		}

		/**
		 * @var int $index
		 * @var Item $item
		 */
		foreach($player->getArmorInventory()->getContents() as $index => $item){
			if($item->getNamedTag()->getString("auctionID", "") !== "" || $item->getNamedTag()->getString("menuItem", "") !== ''){
				$removed[] = $item;
				$player->getInventory()->clear($index);

			}
		}

		if(!empty($removed)){
			$player->sendMessage(Main::PREFIX . "Possible duped items have been found and removed from your inventory:");
			foreach($removed as $i){
				LogHandler::getInstance()->log(new DupeLog($player->getName(), $i));

				$player->sendMessage(" §7- §c" . $i->getName());
			}
		}

		return !empty($removed);
	}

	public static function getTotalItemCount(Item $item, Inventory $inventory): int {
		$c = 0;
		foreach($inventory->getContents() as $content){
			if($content->equals($item)){
				$c += $content->getCount();
			}
		}

		return $c;
	}

	public static function getRandomColor(): string {
		return self::$colors[array_rand(self::$colors)];
	}
}