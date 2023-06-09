<?php

declare(strict_types=1);

namespace skyblock\listeners;

use alvin0319\CustomItemLoader\CustomItemManager;
use kingofturkey38\voting38\events\PlayerVoteEvent;
use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\world\WorldLoadEvent;
use pocketmine\inventory\PlayerInventory;
use pocketmine\inventory\transaction\action\DropItemAction;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionStopBreak;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionWithBlockInfo;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\World;
use RedisClient\Pipeline\PipelineInterface;
use skyblock\caches\combat\CombatCache;
use skyblock\caches\playtime\PlayTimeCache;
use skyblock\caches\pvpzones\PvpZonesCache;
use skyblock\communication\CommunicationData;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\operations\mechanics\BragAddOperation;
use skyblock\communication\operations\mechanics\SeeItemAddOperation;
use skyblock\communication\packets\types\mechanics\brag\AddBragPacket;
use skyblock\communication\packets\types\mechanics\item\AddItemPacket;
use skyblock\communication\packets\types\server\ServerMessagePacket;
use skyblock\communication\packets\types\server\ExecuteCommandPacket;
use skyblock\Database;
use skyblock\entity\boss\JosephBoss;
use skyblock\entity\boss\WitheredBlazeBoss;
use skyblock\events\player\PlayerCombatEnterEvent;
use skyblock\islands\Island;
use skyblock\items\lootbox\types\VoteLootbox;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\MoneyNoteItem;
use skyblock\items\special\types\SkyblockMenuItem;
use skyblock\logs\LogHandler;
use skyblock\logs\types\ChatLog;
use skyblock\logs\types\CommandLog;
use skyblock\Main;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\misc\skills\CombatSkill;
use skyblock\misc\vote\VoteGoal;
use skyblock\misc\warps\WarpHandler;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\ScoreboardUtils;
use skyblock\utils\Utils;
use slapper\entities\SlapperEntity;
use SOFe\AwaitGenerator\Await;

class AetherPEListener implements Listener {

	use PlayerCooldownTrait;



	/**
	 * @param PlayerJoinEvent $event
	 * @priority LOWEST
	 */
	public function onJoin(PlayerJoinEvent $event): void {
		/** @var AetherPlayer $player */
		$player = $event->getPlayer();

		$session = new Session($player);
		if(!$session->playerExists()){
			$session->create($player, true);
		}

		$result = Database::getInstance()->getRedis()->pipeline(function(PipelineInterface $pipeline) use ($player){
			$username = $player->getCurrentProfilePlayerSession()->getUsername();

			$pipeline->get("player.{$username}.inventory");
			$pipeline->get("player.{$username}.armorInventory");
			$pipeline->get("player.{$username}.enderchest");
			$pipeline->get("player.{$username}.minecraftXp");
		});

		$inventoryData = $result[0] ?? "{}";
		$armorInventoryData = $result[1] ?? "{}";
		$enderchestInventoryData = $result[2] ?? "{}";
		$minecraftXp = intval($result[3] ?? 0);

		$session->loadInventory(json_decode($inventoryData, true));
		$session->loadArmorInventory(json_decode($armorInventoryData, true));
		$session->loadEnderchest(json_decode($enderchestInventoryData, true));
		$session->updatePerms();
		CombatCache::getInstance()->removeFromCombat($player);
		QuestHandler::getInstance()->checkDailyQuests($player);

		$player->getXpManager()->setCurrentTotalXp($minecraftXp);
		$player->xpLoaded = true;

		PlayTimeCache::getInstance()->set($player->getName(), time());

		if(Utils::isHubServer()){
			$player->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSafeSpawn());
		}

		$player->getInventory()->setItem(8, SkyblockItems::SKYBLOCK_MENU_ITEM());

		if($data = CommunicationData::getWarpingData($player->getName())){
			$teleported = false;
			CommunicationData::setWarpingData($player->getName(), null);
			$island = new Island($data);
			if($island->exists()){
				if($warp = $island->getWarp()){
					if($world = $island->getWorld()){
						$teleported = true;
						$player->teleport(Position::fromObject($warp, $world));
						$player->sendMessage(Main::PREFIX . "Warped to island {$island->getCaseSensitiveName()}");
						return;
					}
				}
			}

			if($teleported === false){
				$player->sendMessage(Main::PREFIX . "An error occurred, teleporting you to spawn");
				Utils::hub($player);
				return;
			}
		}



		if(($data = CommunicationData::getTeleportingData($player->getName()))){
			unset(CommunicationData::$teleporting[$player->getName()]);

			if(time() - $data["unix"] <= 10){
				if(isset($data["warpName"])){
					Await::f2c(function() use($data, $player){
						$all = yield WarpHandler::getInstance()->getAllWarps();
						if(isset($all[$data["warpName"]])){
							$all[$data["warpName"]]->teleport($player);
							return;
						}

						$player->sendMessage(Main::PREFIX . "Warp named §c" . $data["warpName"] . " doesn't exists");
					});

					return;
				}

				if(isset($data["targetPlayer"])){
					$p = Server::getInstance()->getPlayerExact($data["targetPlayer"]);
					if($p === null){
						$player->sendMessage(Main::PREFIX . "§c" . $data["targetPlayer"] . "§7 is not online anymore");
						return;
					}

					$player->teleport($p->getPosition());

					return;
				}
			}
		}



		if(Utils::isIslandServer()){
			$name = $session->getIslandName();
			if($name !== null){
				$island = new Island($name);

				$world = Server::getInstance()->getWorldManager()->getWorldByName($island->getWorldName());
				if($world instanceof World){
					$player->teleport($world->getSpawnLocation());
				}
			}
		}
	}

	public function onQuit(PlayerQuitEvent $event): void {
		$player = $event->getPlayer();
		assert($player instanceof AetherPlayer);

		$event->setQuitMessage("");

		$session = $player->getCurrentProfilePlayerSession();
		$session->saveEverything($player);

		PlayTimeCache::getInstance()->remove($player->getName());

		ScoreboardUtils::clearCache($player);
	}

	public function onWorldLoad(WorldLoadEvent $event): void {
		$world = $event->getWorld();

		$world->setTime(World::TIME_DAY);
		$world->stopTime();
	}

	/**
	 * @param PlayerChatEvent $event
	 * @priority HIGH
	 */
	public function onChat(PlayerChatEvent $event): void {
		/** @var AetherPlayer $player */
		$player = $event->getPlayer();
		$message = $event->getMessage();


		if($this->isOnCooldown($player) && !Server::getInstance()->isOp($player->getName())){
			$player->sendMessage(Main::PREFIX . "You're sending messages to fast, please slow down");
			$event->cancel();
			return;
		}

		$this->setCooldown($player, 2);

		if(strpos($message, "[brag]") !== false) {
			$message = str_replace("[brag]",TextFormat::GOLD . $player->getName() . "'s inventory" . TextFormat::RESET, $message);
			CommunicationLogicHandler::getInstance()->sendPacket(new AddBragPacket(
				strtolower($player->getName()),
				$player->getInventory()->getContents(true),
				$player->getArmorInventory()->getContents(true),
			));
		}

		if(strpos($message, "[item]") !== false) {
			$item = $player->getInventory()->getItemInHand();
			$message = str_replace("[item]", TextFormat::WHITE . "> " . ($item->hasCustomName() ? $item->getCustomName() : $item->getName()) . TextFormat::GRAY . "(x" . $item->getCount() . ")" . TextFormat::RESET . " <" . TextFormat::RESET, $message);
			CommunicationLogicHandler::getInstance()->sendPacket(new AddItemPacket(strtolower($player->getName()), $item));
		}

		$session = new Session($player);
		$rank = $session->getTopRank();

		$event->setMessage($message);

		$msg = str_replace("{msg}", $event->getMessage(), $rank->getFormat());
		$msg = str_replace("{display_name}", $player->getName(), $msg);
		//$msg = str_replace("{display_name}", ($player->nick ? "~{$player->nick}" : $player->getName()), $msg);


		$event->setFormat($msg);

		/*if($player->islandChat && ($island = $session->getIslandOrNull()) !== null){
			$island->announce("§r§l§aISLAND CHAT §r§a{$player->getName()}: §r§7§o{$event->getMessage()}");
			LogHandler::getInstance()->log(new ChatLog($player, "[ISLAND] " .  $event->getMessage()));

			$event->cancel();
		} else Main::getInstance()->getCommunicationLogicHandler()->sendPacket(new ServerMessagePacket($event->getFormat(), [], [Utils::getServerName()]));*/
		Main::getInstance()->getCommunicationLogicHandler()->sendPacket(new ServerMessagePacket($event->getFormat(), [], [Utils::getServerName()]));

		LogHandler::getInstance()->log(new ChatLog($player, $event->getMessage()));
		QuestHandler::getInstance()->increaseProgress(IQuest::CHAT, $player, $session, $event->getMessage());
	}


	/**
	 * @param PlayerVoteEvent $event
	 * @priority LOW
	 */
	public function onVote(PlayerVoteEvent $event): void {
		/** @var AetherPlayer $player  TODO:
		$player = $event->getPlayer();

		$event->setGiveRewards(false);

		$amount = VoteGoal::getInstance()->increaseVoteGoal(1);
		QuestHandler::getInstance()->increaseProgress(IQuest::VOTE, $player, new Session($player));
		Utils::addItem($player, VoteLootbox::getItem());
		Utils::addItem($player, MoneyNoteItem::getItem(5000, "CONSOLE"));
		Utils::announce("\n" . Main::PREFIX . "§c{$player->getName()}§7 has voted and received 24 hours of free fly, a vote lootbox and $5,000" . "\n");


		if($amount >= 100){
			VoteGoal::getInstance()->setVoteGoal(0);
			Utils::announce("\n" . Main::PREFIX . "Vote goal has been reached, everyone online got an Aether Galaxy Crate" . "\n");

			CommunicationLogicHandler::getInstance()->sendPacket(new ExecuteCommandPacket(
				array_map(fn(string $p) => "crate \"$p\" aether", Utils::getOnlinePlayerUsernames())
			));
		}*/
	}
	
	public function onCombatEnter(PlayerCombatEnterEvent $event): void {
		$player = $event->getPlayer();
		
		if($player->isSurvival() && $player->isFlying()){
			$player->setFlying(false);
			$player->sendMessage(Main::PREFIX . "§cDisabled §7fly");
		}
	}

	/**
	 * @param EntityDamageEvent $event
	 * @priority LOW
	 */
	public function onEntityDamage(EntityDamageEvent $event): void {
		$e = $event->getEntity();

		if($event->getEntity() instanceof SlapperEntity) {
			$event->cancel();
			return;
		}

		if(!$e instanceof Player) return;

		if($event->getCause() === EntityDamageEvent::CAUSE_PROJECTILE){
			$event->setBaseDamage($event->getBaseDamage() / 5);
		}

		if($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
			$event->cancel();
			return;
		}

		if($event->getCause() === EntityDamageEvent::CAUSE_VOID){
			$e->teleport($e->getWorld()->getSpawnLocation());
			$event->cancel();
            return;
		}

		if(str_contains($e->getWorld()->getDisplayName(), "a-")){
			return;
		}

		if(strtolower($e->getWorld()->getFolderName()) === "cosmic"){ //ruins
			if($event instanceof EntityDamageByEntityEvent){
				if($event->getDamager() instanceof Player){
					$event->cancel();
					return;
				}
			}
		}

        if (!PvpZonesCache::getInstance()->isPvpEnabled($e->getPosition())) {
			if(strtolower($event->getEntity()->getWorld()->getDisplayName()) === "nether" && $event->getEntity()->getPosition()->getY() <= 105){
				if($event instanceof EntityDamageByEntityEvent){
					if($event->getDamager() instanceof JosephBoss || $event->getDamager() instanceof WitheredBlazeBoss){
						return;
					}
				}
			}
            $event->cancel();
        }
	}


	public function onCommand(PlayerCommandPreprocessEvent $event): void {
		$player = $event->getPlayer();
		$msg = $event->getMessage();
		
		if (str_starts_with($msg, '/') || str_starts_with($msg, './')) {
			if(CombatCache::getInstance()->isInCombat($event->getPlayer()) && (str_contains($msg, "pv") || str_contains($msg, "playervault"))){
				$event->cancel();
				$player->sendMessage(Main::PREFIX . "You cannot use player vaults while in combat!");
				return;
			}
			
			LogHandler::getInstance()->log(new CommandLog($event->getPlayer(), $event->getMessage()));
		}
	}

	/**
	 * @param BlockBreakEvent $event
	 * @priority MONITOR
	 * @return void
	 */
	public function onBlockBreak(BlockBreakEvent $event): void {
		if($event->isCancelled()) return;
		/** @var AetherPlayer $player */
		$player = $event->getPlayer();
		$drops = $event->getDrops();

		$dropOnGround = true;
		if($player->getSkillData()->getSkillLevel(CombatSkill::id()) >= 6){
			$dropOnGround = false;
		}

		$item = $event->getItem();
		if($item instanceof SkyblockItem){
			$item->onCustomDestroyBlock($player, $event);
		}

		if($event->isCancelled()) return;

		var_dump("drops: ", $drops);
		foreach($drops as $k => $drop){
			if($dropOnGround){
				$drop->getNamedTag()->setByte("collection_drop",  1);
			} else {
				unset($drops[$k]);

				Utils::addItem($player, $drop, true, true);
			}
		}

		$event->setDrops($drops);
	}

	/**
	 * @param EntityItemPickupEvent $event
	 * @priority MONITOR
	 *
	 * @return void
	 */
	public function onItemPickup(EntityItemPickupEvent $event): void {
		$item = $event->getItem();
		$itemEntity = $event->getOrigin();
		$player = $event->getEntity();

		if(!$player instanceof AetherPlayer) return;

		if(!$player->getInventory()->canAddItem($item)) return;

		if($item->getNamedTag()->getByte("collection_drop", 0) === 1){
			$item->getNamedTag()->removeTag("collection_drop");
			$event->setItem($item);

			Utils::addItem($player, $item, false, true);
		} else {
			Utils::addItem($player, $item, false, false);
		}

		$itemEntity->flagForDespawn();
		$event->cancel();
	}

	public function onInventoryTransaction(InventoryTransactionEvent $event): void {
		$trans = $event->getTransaction();
		$p = $trans->getSource();

		foreach($trans->getInventories() as $inventory){
			if(!$inventory instanceof PlayerInventory){
				continue;
			}

			foreach($trans->getActions() as $action){
				if($action instanceof SlotChangeAction){
					if($action->getSlot() === 8){
						$event->cancel();
						return;
					}
				}

				if($action instanceof DropItemAction){
					if($p->getInventory()->getHotbarSlotItem(8)->equals($action->getTargetItem())){
						$event->cancel();
						return;
					}
				}
			}
		}
	}
}