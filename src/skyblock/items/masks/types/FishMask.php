<?php

declare(strict_types=1);

namespace skyblock\items\masks\types;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\WaterDripParticle;
use pocketmine\world\sound\BucketFillWaterSound;
use skyblock\events\pve\PveAttackPlayerEvent;
use skyblock\items\ItemEditor;
use skyblock\items\masks\IMaskHolder;
use skyblock\items\masks\Mask;
use skyblock\items\PvEItemEditor;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\traits\PlayerCooldownTrait;

//this is tested and works fine
class FishMask extends Mask {
	use PlayerCooldownTrait;

	public function getDesiredEvents() : array{
		return [PlayerToggleSneakEvent::class];
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}

	public static function getItem() : Item{
		$item = SkyblockItems::MASK_ITEM();
		$item->setMask(self::getName());

		$item->setCustomName("§r§l§fMask \"§r§f§lFish Mask§f§l\"");
		$item->getProperties()->setDescription([
			"§r§bCrouching whille cause you to splash",
			"§r§bwater around.",
			"§r§8Mana cost: §310",
			"§r§8Cooldown: §a1s",
			"§r",
			"§r§7§oAttach this mask to any helmet",
			"§r§7§oto give it a visual override!",
			"§r",
			"§r§7To equip, place this mask on a helmet.",
			"§r§7To remove, use /removemask while holding the helmet.",
		]);

		PvEItemEditor::setHealth($item, 5);

		self::addNameTag($item);


		return $item;
	}

	public static function getName() : string{
		return "fishmask";
	}

	public function getBlock() : Block{
		return VanillaBlocks::WATER();
	}

	public function getFormat() : string{
		return "§r§f§lFish Mask";
	}

	public function tryCall(Event $event) : void{
		if($event instanceof PlayerToggleSneakEvent){
			$p = $event->getPlayer();
			assert($p instanceof AetherPlayer);

			$item = $p->getArmorInventory()->getHelmet();
			if($item instanceof IMaskHolder && $item->getMask() instanceof $this){
				if($this->isOnCooldown($p)){
					return;
				}

				if($p->getPveData()->getIntelligence() < 10){
					return;
				}


				$p->getPveData()->setIntelligence($p->getPveData()->getIntelligence() - 10);
				$this->onActivate($p, $event);

				$this->setCooldown($p, 1);
			}
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		assert($event instanceof PveAttackPlayerEvent);

		$bb = $player->getBoundingBox()->expandedCopy(1, 0, 1);

		for($x = $bb->minX; $x <= $bb->maxX; $x++){
			for($z = $bb->minZ; $z <= $bb->maxZ; $z++){

				$r = mt_rand(1, 2);
				for($i = 0; $i <= $r; $i++){
					$addX = mt_rand(1, 10) / (mt_rand(1, 2) === 1 ? 10 : -10);
					$addZ = mt_rand(1, 10) / (mt_rand(1, 2) === 1 ? 10 : -10);

					$player->getWorld()->addParticle(new Vector3($x + $addX, $player->getPosition()->getY(), $z + $addZ), new WaterDripParticle());
				}
			}
		}
		$player->getWorld()->addSound($player->getPosition(), new BucketFillWaterSound());
	}



	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onWear(Player $player, Item $old, Item $new) : bool{
		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + 5);
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
		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() - 5);
		return true;
	}
}