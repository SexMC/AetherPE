<?php

declare(strict_types=1);

namespace skyblock\items\special\types\fishing;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\events\player\PlayerFishEvent;
use skyblock\events\player\PlayerPreFishEvent;
use skyblock\utils\PveUtils;

class MinnowBait extends Bait {

	public static function getItem(): Item {
		$item = VanillaItems::SLIMEBALL();
		$item->setCustomName("§r§fMinnow Bait");
		$item->setLore([
			"§r§8Fishing Bait",
			"§r§8Consumes on cast",
			"§r",
			"§r§7Grants §a+25 §bFishing Speed",
			"§r",
			"§r§f§lCOMMON BAIT"
		]);

		self::addNameTag($item);

		return $item;
	}

	public function onPreFish(PlayerPreFishEvent $event) : bool{
		$event->setFishingSpeedMultiplier($event->getFishingSpeedMultiplier() - (25 / 350));

		return true;
	}

	public function onFish(PlayerFishEvent $event) : bool{
		return false;
	}

	public static function getItemTag() : string{
		return "MinnowBait";
	}
}