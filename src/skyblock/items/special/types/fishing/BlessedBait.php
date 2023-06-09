<?php

declare(strict_types=1);

namespace skyblock\items\special\types\fishing;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\events\player\PlayerFishEvent;
use skyblock\events\player\PlayerPreFishEvent;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;
use skyblock\utils\Utils;

class BlessedBait extends Bait {

	public static function getItem(): Item {
		$item = VanillaItems::SLIMEBALL();
		$item->setCustomName("§r§aBlessed Bait");
		$item->setLore([
			"§r§8Fishing Bait",
			"§r§8Consumes on cast",
			"§r",
			"§r§7Increases double drop changes",
			"§r§7by §a10%§7 for fishing.",
			"§r",
			"§r§a§lUNCOMMON BAIT"
		]);

		self::addNameTag($item);

		return $item;
	}

	public function onPreFish(PlayerPreFishEvent $event) : bool{
		return false;
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
		return "BlessedBait";
	}
}