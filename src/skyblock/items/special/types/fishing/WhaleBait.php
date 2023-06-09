<?php

declare(strict_types=1);

namespace skyblock\items\special\types\fishing;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\events\player\PlayerFishEvent;
use skyblock\events\player\PlayerPreFishEvent;
use skyblock\utils\PveUtils;

class WhaleBait extends Bait {

	public static function getItem(): Item {
		$item = VanillaItems::SLIMEBALL();
		$item->setCustomName("§r§3Whale Bait");
		$item->setLore([
			"§r§8Fishing Bait",
			"§r§8Consumes on cast",
			"§r",
			"§r§7Grants §a+45 §bFishing Speed,",
			"§r§7increases double drop chances",
			"§r§7by §a10% §7for fishing and",
			"§r§7increases your chance to",
			"§r§7catch rare Sea Creatures.",
			"§r",
			"§r§3§lRARE BAIT"
		]);

		self::addNameTag($item);

		return $item;
	}

	public function onPreFish(PlayerPreFishEvent $event) : bool{
		$event->setFishingSpeedMultiplier($event->getFishingSpeedMultiplier() - (45 / 350));

		return true;
	}

	public function onFish(PlayerFishEvent $event) : bool{
		$rewards = $event->getRewards();

		if(mt_rand(1, 10) === 1){
			foreach($rewards as $reward){
				$reward->setCount($reward->getCount() * 2);
			}

			return true;
		}

		return false;
	}

	public static function getItemTag() : string{
		return "WhaleBait";
	}
}