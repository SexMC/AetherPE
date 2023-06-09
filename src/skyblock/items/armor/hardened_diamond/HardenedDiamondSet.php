<?php

declare(strict_types=1);

namespace skyblock\items\armor\hardened_diamond;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\PveUtils;

class HardenedDiamondSet extends ArmorSet {
	use AetherHandlerTrait;

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getItemAttributes(string $piece) : array{
		$arr = [];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 55);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 120);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 95);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 60);
				break;
		}

		return $arr;
	}

	public function getAbilities() : array{
		return [];
	}

	public function getIdentifier() : string{
		return "hardened_diamond_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§3Hardened Diamond " . $piece;
	}

	public function getLore(Item $item) : array{
		return [
			"§r§l§3Set Bonus: §3Hardened Diamond",
			"§r§l§3 » §r§3Just really shiny Diamond Armor §r§l§3«",
		];
	}


	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::HARDENED_DIAMOND_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::HARDENED_DIAMOND_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::HARDENED_DIAMOND_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::HARDENED_DIAMOND_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::rare();
	}
}