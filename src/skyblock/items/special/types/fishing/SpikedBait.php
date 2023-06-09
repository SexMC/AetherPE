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

class SpikedBait extends Bait {

	public static function getItem(): Item {
		$item = VanillaItems::SLIMEBALL();
		$item->setCustomName("§r§fSpiked Bait");
		$item->setLore([
			"§r§8Fishing Bait",
			"§r§8Consumes on cast",
			"§r",
			"§r§7Grants §a+6 §3Sea Creature Chance",
			"§r",
			"§r§f§lCOMMON BAIT"
		]);

		self::addNameTag($item);

		return $item;
	}

	public function onPreFish(PlayerPreFishEvent $event) : bool{
		return false;
	}

	public function onFish(PlayerFishEvent $event) : bool{
		$p = $event->getPlayer();

		assert($p instanceof AetherPlayer);

		$p->getPveData()->setSeacreatureChance($p->getPveData()->getSeacreatureChance() + 6);

		Utils::executeLater(function() use ($p){
			$p->getPveData()->setSeacreatureChance($p->getPveData()->getSeacreatureChance() - 6);
		}, 2);

		return true;
	}

	public static function getItemTag() : string{
		return "SpikedBait";
	}
}