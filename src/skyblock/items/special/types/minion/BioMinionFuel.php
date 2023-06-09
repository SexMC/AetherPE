<?php


declare(strict_types=1);

namespace skyblock\items\special\types\minion;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\items\special\SpecialItem;

class BioMinionFuel extends SpecialItem {
	
	public static function getItem(): Item {
		$item = VanillaItems::HEART_OF_THE_SEA();
		$item->setCustomName("§l§dMinion Fuel (§aBio§d)");
		$item->setLore([
			"§r§7§oI see, you want a low carbon footprint",
			"§r§7§oI appreciate you using environmental",
			"§r§7§ofriendly fuels.",
			"§r",
			"§r§l§dFUEL TYPE",
			"§r§aBio",
			"§r",
			"§r§l§dFUEL DURATION",
			"§r§a8 hours",
			"§r",
			"§r§c§lNOTE: §r§cOnce put in a minion",
			"§r§cit cannot be retrieved back.",
		]);

		self::addNameTag($item);
		self::addUniqueIdNameTag($item);

		return $item;
	}
	
	public static function getItemTag() : string{
		return "BioMinionFuel";
	}
}