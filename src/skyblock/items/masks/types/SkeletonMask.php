<?php

declare(strict_types=1);

namespace skyblock\items\masks\types;

use pocketmine\block\Block;
use pocketmine\block\utils\SkullType;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\particle\HugeExplodeParticle;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ItemEditor;
use skyblock\items\masks\IMaskHolder;
use skyblock\items\masks\Mask;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;

//this is tested and works fine
class SkeletonMask extends Mask {
	public function getDesiredEvents() : array{
		return [ProjectileHitEvent::class];
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}

	public static function getItem() : Item{
		$item = SkyblockItems::MASK_ITEM();
		$item->setMask(self::getName());
		$item->setCustomName("§r§l§fMask \"§r§a§lSkeleton Mask§f§l\"");
		$item->getProperties()->setDescription([
			"§r§a+2 " . PveUtils::getSpeed(),
			"§r§a+10 " . PveUtils::getIntelligence(),
			"§r",
			"§r§bYour arrows have a §a20%§b chance to explode",
			"§r§bon impact dealing §a50§b base magic damage",
			"§r§bto enemies within §a8§b blocks.",
			"§r",
			"§r§7§oAttach this mask to any helmet",
			"§r§7§oto give it a visual override!",
			"§r",
			"§r§7To equip, place this mask on a helmet.",
			"§r§7To remove, use /removemask while holding the helmet.",
		]);

		self::addNameTag($item);


		return $item;
	}

	public static function getName() : string{
		return "skeletonmask";
	}

	public function getBlock() : Block{
		return VanillaBlocks::MOB_HEAD()->setSkullType(SkullType::SKELETON());
	}

	public function getFormat() : string{
		return "§r§a§lSkeleton Mask";
	}

	public function tryCall(Event $event) : void{
		if($event instanceof ProjectileHitEvent){
			$arrow = $event->getEntity();

			if(!$arrow instanceof Arrow) return;

			$p = $arrow->getOwningEntity();

			if(!$p instanceof Player) return;


			$item = $p->getArmorInventory()->getHelmet();
			if($item instanceof IMaskHolder && $item->getMask() instanceof $this && mt_rand(1, 5) === 1){
				$this->onActivate($p, $event);
			}
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		assert($event instanceof ProjectileHitEvent);
		assert($player instanceof AetherPlayer);

		$vector = $event->getRayTraceResult()->getHitVector();

		$player->getWorld()->addParticle($vector, new HugeExplodeParticle());
		foreach($player->getWorld()->getNearbyEntities($event->getEntity()->getBoundingBox()->expandedCopy(8, 8, 8)) as $e){
			if($e instanceof PveEntity){
				$e = (new PlayerAttackPveEvent($player, $e, 50));
				$e->setIsMagicDamage(true);
				$e->call();
			}
		}
	}


	public function listenToCancelled() : bool{
		return true;
	}

	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onWear(Player $player, Item $old, Item $new) : bool{
		$player->getPveData()->setMaxIntelligence($player->getPveData()->getMaxIntelligence() + 10);
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() + 2);
		return true;
	}

	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onTakeOff(Player $player, Item $old, Item $new) : bool{
		$player->getPveData()->setMaxIntelligence($player->getPveData()->getMaxIntelligence() - 10);
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() - 2);
		return true;
	}
}