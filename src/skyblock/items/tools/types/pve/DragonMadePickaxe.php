<?php

declare(strict_types=1);

namespace skyblock\items\tools\types\pve;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\PvEItemEditor;
use skyblock\items\tools\SpecialWeapon;

class DragonMadePickaxe extends SpecialWeapon {

	public function getDesiredEvents() : array{
		return [];
	}

	public static function getItem() : Item{
		$item = VanillaItems::GOLDEN_PICKAXE();
		$item->setUnbreakable();
		$item->setCustomName("DragonMadePickaxe");
		$item->setLore((new self())->getExtraLore());
		PvEItemEditor::setMiningSpeed($item, 200);
		PvEItemEditor::setMiningFortune($item, 75);

		//TODO: usable at only lvl 10 bleeding city reputation lvl
		self::addNametag($item);

		return $item;
	}

	public static function getName() : string{
		return "DragonMadePickaxe";
	}

	public function getExtraLore() : array{
		return [
			"TODO FOR DEMONIC"
		];
	}

	public function tryCall(Event $event) : void{}
	public function onActivate(Player $player, Event $event) : void{}
}