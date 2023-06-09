<?php

declare(strict_types=1);

namespace skyblock\listeners;

use pocketmine\block\Bedrock;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Dirt;
use pocketmine\block\Grass;
use pocketmine\block\Leaves;
use pocketmine\block\Slime;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\animation\HurtAnimation;
use pocketmine\entity\effect\SpeedEffect;
use pocketmine\entity\object\ItemEntity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDespawnEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\world\ChunkLoadEvent;
use pocketmine\item\EnderPearl;
use pocketmine\item\Hoe;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\PlayerEnchantOptionsPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\world\format\BiomeArray;
use pocketmine\world\format\Chunk;
use pocketmine\world\sound\BlazeShootSound;
use pocketmine\world\sound\XpLevelUpSound;
use skyblock\customenchants\rare\Haste;
use skyblock\entity\boss\PveEntity;
use skyblock\events\player\PlayerFishEvent;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\events\pve\PlayerKillPveEvent;
use skyblock\events\pve\PveAttackPlayerEvent;
use skyblock\events\pve\PveKillPlayerEvent;
use skyblock\events\skills\SkillLevelupEvent;
use skyblock\items\crates\Crate;
use skyblock\items\SkyblockItem;
use skyblock\items\special\types\SeaBossEggItem;
use skyblock\Main;
use skyblock\misc\pve\fishing\HotspotHandler;
use skyblock\misc\pve\PveBossbarUpdater;
use skyblock\misc\pve\PveHandler;
use skyblock\misc\pve\PveTipUpdater;
use skyblock\misc\pve\zone\ForagingZone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\misc\skills\CombatSkill;
use skyblock\misc\skills\FarmingSkill;
use skyblock\misc\skills\FishingSkill;
use skyblock\misc\skills\ForagingSkill;
use skyblock\misc\skills\MiningSkill;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\EntityUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class PveListener implements Listener {
	use AwaitStdTrait;

	public function onJoin(PlayerJoinEvent $event): void {
		/** @var AetherPlayer $player */
		$player = $event->getPlayer();
		Await::f2c(function() use($player){
			yield $this->getStd()->sleep(20);

			if(!$player->isOnline()) return;
			$player->setPveBossbarUpdater(new PveBossbarUpdater($player));
			var_dump("sets bosbar");
		});
	}

	public function onSkillLevelup(SkillLevelupEvent $event): void {
		$player = $event->getPlayer();

		$player->getPveBossbarUpdater()?->updateTitle();
		$player->getPveBossbarUpdater()?->updateBossBar();
		$player->getWorld()->addSound($player->getPosition(), new XpLevelUpSound(30), [$player]);
	}

	public function onPlayerInteract(PlayerInteractEvent $event): void {
		$player = $event->getPlayer();
		$action = $event->getAction();
		$item = $event->getItem();

		if($player->getWorld()->getId() === PveHandler::getInstance()->getPveWorld()->getId()){
			if($item instanceof Hoe && $action === PlayerInteractEvent::RIGHT_CLICK_BLOCK){
				if($player->isSurvival()){
					$event->cancel();
				}
			}
		}
	}




	/**
	 * @param BlockBreakEvent $event
	 * @priority HIGHEST
	 * @handleCancelled true
	 */
	public function onBlockBreak(BlockBreakEvent $event): void {
		$block = $event->getBlock();
		/** @var AetherPlayer $player */
		$player = $event->getPlayer();


		if($player->getWorld()->getId() !== PveHandler::getInstance()->getPveWorld()->getId()) return;
		if($player->isCreative()) return;


			if(FarmingSkill::isCropBlock($block->getId(), $block->getMeta())){
				$xp = FarmingSkill::getXpByCrop($block->getId());

				$player->getSkillData()->increaseSkillXp(FarmingSkill::id(), $xp);

				//foreach($event->getDrops() as $drop){
				//	Utils::addItem($player, $drop);
				//}

				Utils::executeLater(function() use($block) {
					$world = PveHandler::getInstance()->getPveWorld();

					if($world->isLoaded()){
						$world->setBlock($block->getPosition(), BlockFactory::getInstance()->get($block->getId(), $block->getMeta()));
					}
				}, mt_rand(250, 650), true);

				$player->getPveBossbarUpdater()?->setType(PveBossbarUpdater::TYPE_FARMING_SKILL);


				$event->uncancel();
			}


		if(($xpDrop = MiningSkill::getXpDropByBlock($block)) > 0){
			$player->getSkillData()->increaseSkillXp(MiningSkill::id(), $xpDrop + $player->getPveData()->getMiningWisdom());
			$player->getPveBossbarUpdater()?->setType(PveBossbarUpdater::TYPE_MINING_SKILL);

			$drops = $block->getDrops($player->getInventory()->getItemInHand());

			$miningFortune = $player->getPveData()->getMiningFortune();
			$add = (int) floor($miningFortune / 100);
			$rest = $miningFortune % 100;
			foreach($drops as $drop){
				if($miningFortune > 100){
					$drop->setCount($drop->getCount() + $add);
				}

				if(mt_rand(1, 100) <= $rest){
					$drop->setCount($drop->getCount() + 1);
				}
			}
			$event->setDrops($drops);

			Utils::executeLater(function() use($block) {
				$world = PveHandler::getInstance()->getPveWorld();

				if($world->isLoaded()){
					$world->setBlock($block->getPosition(), BlockFactory::getInstance()->get($block->getId(), $block->getMeta()));
				}
			}, mt_rand(250, 650), true);

			//foreach($event->getDrops() as $drop){
			//	Utils::addItem($player, $drop);
			//}
			Utils::executeLater(function() use ($block, $player) {
				$player->getWorld()->setBlock($block->getPosition(), VanillaBlocks::BEDROCK());
			}, 1);
			$event->uncancel();
		}

		if(($xpDrop = ForagingSkill::getXpDropByBlock($block)) > 0){
			if(!ZoneHandler::getInstance()->getZoneByVector($block->getPosition()) instanceof ForagingZone){
				$event->cancel();
				return;
			}
			

			$player->getSkillData()->increaseSkillXp(ForagingSkill::id(), $xpDrop + $player->getPveData()->getForagingWisdom());
			$player->getPveBossbarUpdater()?->setType(PveBossbarUpdater::TYPE_FORAGING_SKILL);
			$event->uncancel();
			
			$world = $player->getWorld();
			$block = $event->getBlock();
			$pos = $block->getPosition();

			$foragingFortune = $player->getPveData()->getForagingFortune();
			$drops = $block->getDrops($player->getInventory()->getItemInHand());

			//foraging fortune logic start
			$add = (int) floor($foragingFortune / 100);
			$rest = $foragingFortune % 100;
			foreach($drops as $drop){
				if($foragingFortune > 100){
					$drop->setCount($drop->getCount() + $add);
				}

				if(mt_rand(1, 100) <= $rest){
					$drop->setCount($drop->getCount() + 1);
				}
			}
			//foraging fortune logic end

			//foreach($drops as $drop){
			//	Utils::addItem($player, $drop);
			//}

			$event->setDrops($drops);
			$world->setBlock($block->getPosition(), VanillaBlocks::AIR());
			Utils::executeLater(function() use($pos, $block, $world) {
				$world->setBlock($pos, $block);
			}, 60 * mt_rand(5, 10), true);
		}

		//TODO: checkout new java map
		// gold mine coords 125 125 125
	}

	public function onFish(PlayerFishEvent $event): void {
		/** @var AetherPlayer $player */
		$player = $event->getPlayer();
		$hook = $event->getFishingHook();

		$handler = HotspotHandler::getInstance();

		if(!$handler->isInsideHotspot($hook->getPosition())){
			return;
		}

		$level = $player->getSkillData()->getSkillLevel(FishingSkill::id());
		$boost = $handler->getBoost();

		$treasureChance = $level * 0.4;
		$bossChance = $level * 0.005;

		$bossChance += $boost->extraSeaBossSpawnEggChance;
		$treasureChance += $boost->extraTreasureLootChance;

		if((mt_rand(1, 10000) / 100) <= $treasureChance){

			//TODO: check this fishing
			$crates = [CommonCrate::getInstance(), RareCrate::getInstance()];

			if(mt_rand(1, 3) === 1){
				$crates[] = [LegendaryCrate::getInstance()];
			}

			/** @var Crate $rand */
			$rand = $crates[array_rand($crates)];
			$reward = $rand->getRandomReward();

			$event->setRewards([$reward]);
		}

		if((mt_rand(1, 10000) / 100) <= $bossChance){
			$boss = [
				SeaBossEggItem::getItem(SeaBossEggItem::TYPE_GIBLE),
				SeaBossEggItem::getItem(SeaBossEggItem::TYPE_SEAL),
				SeaBossEggItem::getItem(SeaBossEggItem::TYPE_PATRICK),
			];

			$event->setRewards([$boss]);
		}
	}



	/**
	 * @param PveAttackPlayerEvent $event
	 * @priority  NORMAL
	 */
	public function onPveAttackPlayer(PveAttackPlayerEvent $event): void {
		$player = $event->getPlayer();
		$entity = $event->getEntity();
		$damage = $event->getFinalDamage();

		$defense = $player->getPveData()->getDefense();


		$reduceDamage = $defense / ($defense + 100);
		$newDamage = max(0, $damage * (1 - $reduceDamage));

		$health = $player->getPveData()->getHealth();
		if($health <= $newDamage){
			$player->teleport($player->getWorld()->getSpawnLocation());
			$player->sendMessage(Main::PREFIX . "You died while fighting §c" . $entity->getName() . ".");
			$player->sendMessage(Main::PREFIX . "Fortunately, you have not lost anything.");

			$player->getPveData()->setHealth($player->getPveData()->getMaxHealth());


			(new PveKillPlayerEvent($player, $entity, $event))->call();
			return;
		}

		$health -= $newDamage;
		$player->getPveData()->setHealth($health);


		$deltaX = $player->getPosition()->x - $entity->getPosition()->x;
		$deltaZ = $player->getPosition()->z - $entity->getPosition()->z;
		$player->knockBack($deltaX, $deltaZ, $event->getKnockback());

		$player->doHitAnimationCustom();
	}

	/**
	 * @param PlayerAttackPveEvent $event
	 * @priority MONITOR
	 *
	 * @return void
	 */
	public function onPlayerAttackPvE(PlayerAttackPveEvent $event): void {
		$player = $event->getPlayer();
		$entity = $event->getEntity();

		//$event->setDamage(mt_rand(80, 150));

		$player->getPveBossbarUpdater()?->setType(PveBossbarUpdater::TYPE_COMBAT_SKILL);

		foreach($entity->abilities as $id){
			$ability = PveHandler::getInstance()->getAbility($id);

			if($ability === null) continue;

			$ability->onDamage($entity, $event);
		}


		$item = $player->getInventory()->getItemInHand();

		if($item instanceof SkyblockItem){
			$item->onAttackPve($player, $event);
		}


		$finalDamage = $event->getFinalDamage();

		$string = number_format($finalDamage, 0);

		if($event->isCritical()){
			$string = "✧{$finalDamage}✧";
		}

		$sending = "";
		foreach(str_split($string) as $v){
			$sending .= Utils::getRandomColor() . $v;
		}

		$entity->setLastDamageSource($event);
		EntityUtils::spawnTextEntity($entity->getLocation(), $sending, 1, [$player]);
		$entity->setHealth($entity->getHealth() - $finalDamage);
		if($entity->hostile){
			if($entity->getTarget() === null){
				$entity->setTarget($player);
			}
		}


		$deltaX = $entity->getLocation()->x - $player->getLocation()->x;
		$deltaZ = $entity->getLocation()->z - $player->getLocation()->z;
		$entity->knockBack($deltaX, $deltaZ, $event->getKnockback());
		$entity->broadcastAnimation(new HurtAnimation($entity));


	}

	/**
	 * @param EntityItemPickupEvent $event
	 * @priority LOW
	 * @return void
	 */
	public function onItemPickup(EntityItemPickupEvent $event): void {
		$p = $event->getEntity();

		if(!$p instanceof AetherPlayer) return;

		$itemEntity = $event->getOrigin();

		if(!$itemEntity instanceof ItemEntity) return;

		$i = $itemEntity->getItem();
		if($i->getNamedTag()->getByte("collection", -1) !== -1){
			$itemEntity->flagForDespawn();
			$event->cancel();

			$i->getNamedTag()->removeTag("collection");
			Utils::addItem($p, $i, false, true);
		}
	}

	public function onPlayerKillPve(PlayerKillPveEvent $event): void {
		$player = $event->getPlayer();
		$entity = $event->getEntity();

		$xp = $entity->combatXp;

		$player->getSkillData()->increaseSkillXp(CombatSkill::id(), $xp  + $player->getPveData()->getCombatWisdom());

		$player->getNetworkSession()->sendDataPacket(EntityUtils::getSoundPacket("random.orb", $player->getLocation()));

		$player->getPveBossbarUpdater()?->setType(PveBossbarUpdater::TYPE_COMBAT_SKILL);

		if($entity->coins > 0){
			$player->getCurrentProfilePlayerSession()->increasePurse($entity->coins);
		}
	}

	/**
	 * @param EntityDespawnEvent $event
	 * @priority NORMAL
	 */
	public function onEntityDespawn(EntityDespawnEvent $event): void {
		$entity = $event->getEntity();

		if($entity instanceof PveEntity) {
			if(($zone = $entity->getZone()) !== null){
				$zone->decreaseMob($entity->getZoneMobName());
			}
		}
	}

}