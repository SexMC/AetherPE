<?php

declare(strict_types=1);

namespace skyblock\items\armor\growth;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\PveUtils;

class GrowthSet extends ArmorSet {
	use AetherHandlerTrait;

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getItemAttributes(string $piece) : array{
		$arr = [];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 40);
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 55);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 50);
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 115);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 10);
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 105);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 40);
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 55);
				break;
		}

		return $arr;
	}

	public function getAbilities() : array{
		return [
			new GrowthSetAbility($this),
		];
	}

	public function getIdentifier() : string{
		return "growth_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§3$piece of Growth";
	}

	public function getLore(Item $item) : array{
		return [
			"§r§l§3Set Bonus: §3Armor Of Growth",
			"§r§l§3 » §r§3Heal §a1% "  . PveUtils::getHealth() . " after Slaying a Monster §r§l§3«",
		];
	}


	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::GROWTH_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::GROWTH_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::GROWTH_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::GROWTH_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::rare();
	}
}