<?php

declare(strict_types=1);

namespace skyblock\items\armor\golem;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\PveUtils;

class GolemSet extends ArmorSet {
	use AetherHandlerTrait;

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getItemAttributes(string $piece) : array{
		$arr = [];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 40);
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 40);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 90);
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 65);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 75);
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 55);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 40);
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 40);
				break;
		}

		return $arr;
	}

	public function getAbilities() : array{
		return [
			new GolemSetAbility($this),
		];
	}

	public function getIdentifier() : string{
		return "golem_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§3Golem Armor " . $piece;
	}

	public function getLore(Item $item) : array{
		return [
			"§r§l§3Set Bonus: §3Absorption",
			"§r§l§3 » §r§3Grants the wearer §6Absorption III §3for",
			"§r§l§3 §r§320 seconds when an enemy is killed. §r§l§3«",
		];
	}

	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::GOLEM_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::GOLEM_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::GOLEM_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::GOLEM_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::rare();
	}
}