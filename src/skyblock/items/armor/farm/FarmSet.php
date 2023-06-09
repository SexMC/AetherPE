<?php

declare(strict_types=1);

namespace skyblock\items\armor\farm;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\PveUtils;

class FarmSet extends ArmorSet {
	use AetherHandlerTrait;

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getItemAttributes(string $piece) : array{
		$arr = [
			new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 20)
		];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 40);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 75);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 50);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 35);
				break;
		}

		return $arr;
	}

	public function onWear(AetherPlayer $player) : void{
		parent::onWear($player);

		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() + 25);
	}

	public function onTakeoff(AetherPlayer $player) : void{
		parent::onTakeoff($player);

		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() - 25);
	}

	public function getAbilities() : array{
		return [];
	}

	public function getIdentifier() : string{
		return "farm_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§3Farmer's Clothing " . $piece;
	}

	public function getLore(Item $item) : array{
		return [
			"§r§l§3Set Bonus: §3Bonus Speed",
			"§r§l§3 » §r§3+25"  . PveUtils::getSpeedSymbol() . " Movement Speed §r§l§3«",
		];
	}

	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::FARM_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::FARM_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::FARM_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::FARM_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::rare();
	}
}