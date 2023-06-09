<?php

declare(strict_types=1);

namespace skyblock\items\masks\types;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\events\pve\PveAttackPlayerEvent;
use skyblock\items\masks\IMaskHolder;
use skyblock\items\masks\Mask;
use skyblock\items\PvEItemEditor;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\traits\AwaitStdTrait;

class SlimeHatMask extends Mask {
	use AwaitStdTrait;

	public function getRarity() : Rarity{
		return Rarity::common();
	}

	public function getDesiredEvents() : array{
		return [PveAttackPlayerEvent::class];
	}

	public static function getItem() : Item{
		$item = SkyblockItems::MASK_ITEM();
		$item->setMask(self::getName());		$item->setCustomName("§r§l§fMask \"§r§a§lSlime Hat§f§l\"");
		$item->getProperties()->setDescription([
			"§r§bGrants immunity to knockback from mobs.",
			"§r",
			"§r§7§oAttach this mask to any helmet",
			"§r§7§oto give it a visual override!",
			"§r",
			"§r§7To equip, place this mask on a helmet.",
			"§r§7To remove, use /removemask while holding the helmet.",
		]);

		PvEItemEditor::setHealth($item, 50);

		self::addNameTag($item);


		return $item;
	}



	public static function getName() : string{
		return "slimehatmask";
	}

	public function getBlock() : Block{
		return VanillaBlocks::SLIME();
	}

	public function getFormat() : string{
		return "§r§a§lSlime Hat Mask";
	}

	public function tryCall(Event $event) : void{
		assert($event instanceof PveAttackPlayerEvent);

		$p = $event->getPlayer();

		$item = $p->getArmorInventory()->getHelmet();
		if($item instanceof IMaskHolder && $item->getMask() instanceof $this){
			$this->onActivate($p, $event);
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		assert($event instanceof PveAttackPlayerEvent);

		$event->setKnockback(0);
	}


	/**
	 * @param AetherPlayer $player
	 * @param Item   $old
	 * @param Item   $new
	 *
	 * @return bool
	 */
	public function onWear(Player $player, Item $old, Item $new) : bool{
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
		return true;
	}
}