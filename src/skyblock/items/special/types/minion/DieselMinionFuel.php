<?php


declare(strict_types=1);

namespace skyblock\items\special\types\minion;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\items\special\SpecialItem;

class DieselMinionFuel extends SpecialItem {

	public static function getItem(): Item {
		$item = VanillaItems::HEART_OF_THE_SEA();
		$item->setCustomName("§l§dMinion Fuel (§r§eDiesel§l§d)");
		$item->setLore([
			"§r§7§oDiesel, not so an environmental friendly",
			"§r§7§ofuel, avoid using this as much as you can",
			"§r",
			"§r§l§dFUEL TYPE",
			"§r§eDiesel",
			"§r",
			"§r§l§dFUEL DURATION",
			"§r§e24 hours",
			"§r",
			"§r§c§lNOTE: §r§cOnce put in a minion",
			"§r§cit cannot be retrieved back.",
		]);

		self::addNameTag($item);
		self::addUniqueIdNameTag($item);

		return $item;
	}

	public static function getItemTag() : string{
		return "DieselMinionFuel";
	}
}