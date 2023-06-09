<?php

declare(strict_types=1);

namespace skyblock\items\armor\pumpkin;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\traits\AetherHandlerTrait;

class PumpkinSet extends ArmorSet {
	use AetherHandlerTrait;

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getItemAttributes(string $piece) : array{
		$arr = [];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 8);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 14);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 10);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 8);
				break;
		}

		return $arr;
	}

	public function getAbilities() : array{
		return [];
	}

	public function getIdentifier() : string{
		return "pumpkin_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§fPumpkin " . $piece;
	}

	public function getLore(Item $item) : array{
		return [ //TODO: ability of this
			"§r§l§fSet Bonus: §fPumpkin Spice",
			"§r§l§f » §r§fDeal §a10% §fMore and Receive Less §r§l§f«",
		];
	}
	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::PUMPKIN_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::PUMPKIN_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::PUMPKIN_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::PUMPKIN_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}
}