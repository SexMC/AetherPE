<?php

declare(strict_types=1);

namespace skyblock\items\armor\farm_suit;

use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\PveUtils;

class FarmSuitSet extends ArmorSet {
	use AetherHandlerTrait;

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getItemAttributes(string $piece) : array{
		$arr = [];

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

	public function onWear(AetherPlayer $player) : void{
		parent::onWear($player);

		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() + 20);
	}

	public function onTakeoff(AetherPlayer $player) : void{
		parent::onTakeoff($player);

		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() - 20);
	}

	public function getAbilities() : array{
		return [];
	}

	public function getIdentifier() : string{
		return "farm_suit_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§fFarmer's Suit " . $piece;
	}

	public function getLore(Item $item) : array{
		return [
			"§r§l§fSet Bonus: §fFarmer Aura",
			"§r§l§f » §r§f+20"  . PveUtils::getSpeedSymbol() . " Movement Speed §r§l§f«",
			"§r§l§f » §r§fCrops harvested will grow faster §r§l§f«",
		];
	}

	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::FARM_SUIT_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::FARM_SUIT_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::FARM_SUIT_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::FARM_SUIT_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::common();
	}
}