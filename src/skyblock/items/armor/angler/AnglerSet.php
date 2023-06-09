<?php

declare(strict_types=1);

namespace skyblock\items\armor\angler;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\PveUtils;

class AnglerSet extends ArmorSet {
	use AetherHandlerTrait;

	public function getItemAttributes(string $piece) : array{
		$arr = [
			new ItemAttributeInstance(SkyBlockItemAttributes::SEA_CREATURE_CHANCE(), 1)
		];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 15);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 40);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 30);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 15);
				break;
		}

		return $arr;
	}

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getAbilities() : array{
		return [
			new AnglerSetAbility($this),
		];
	}

	public function getIdentifier() : string{
		return "angler_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§fAngler " . $piece;
	}

	public function getLore(Item $item) : array{
		return [
			"§r§l§fSet Bonus: §fDepth Champion",
			"§r§l§f » §r§fTake 30% less damage from §lSea Creatures §r§l§f«",
			"§r§l§f » §r§fGain §a+10 " . PveUtils::getHealth() . " §r§fper Fishing Level §r§l§f«",
		];
	}

	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::ANGLER_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::ANGLER_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::ANGLER_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::ANGLER_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}
}