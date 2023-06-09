<?php

declare(strict_types=1);

namespace skyblock\items\armor\lapis;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\Item;
use skyblock\items\armor\ArmorSet;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\sets\SpecialSet;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherEffect;
use skyblock\player\AetherPlayer;
use skyblock\traits\AetherHandlerTrait;
use skyblock\utils\PveUtils;

class LapisSet extends ArmorSet {
	use AetherHandlerTrait;

	public function onEnable() : void{
		ArmorSet::registerSet($this);
	}

	public function getItemAttributes(string $piece) : array{
		$arr = [];

		switch($piece) {
			case self::PIECE_HELMET:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 25);
				break;
			case self::PIECE_CHESTPLATE:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 40);
				break;

			case self::PIECE_LEGGINGS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 35);
				break;
			case self::PIECE_BOOTS:
				$arr[] = new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 20);
				break;
		}

		return $arr;
	}

	public function onWear(AetherPlayer $player) : void{
		parent::onWear($player);

		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + 60);
	}

	public function onTakeoff(AetherPlayer $player) : void{
		parent::onTakeoff($player);

		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() - 60);
	}

	public function getAbilities() : array{
		return [];
	}

	public function getIdentifier() : string{
		return "lapis_set";
	}

	public function getName(string $piece = null) : string{
		return "§r§aLapis Armor " . $piece;
	}

	public function getLore(Item $item) : array{
		return [
			"§r§7Each piece of this armour grants",
			"§r§7§a50%§7 bonus experience when",
			"§r§7mining ores.",
			"§r",
			"§r§6Full Set Bonus: Health",
			"§r§7Increases the wearer's maximum",
			"§r§7" . PveUtils::getHealth() . " §7by §a60"
		];
	}

	public function getPieceItems() : array{
		return [
			self::PIECE_BOOTS => SkyblockItems::LAPIS_LAZULI_BOOTS(),
			self::PIECE_HELMET => SkyblockItems::LAPIS_LAZULI_HELMET(),
			self::PIECE_LEGGINGS => SkyblockItems::LAPIS_LAZULI_LEGGINGS(),
			self::PIECE_CHESTPLATE => SkyblockItems::LAPIS_LAZULI_CHESTPLATE(),
		];
	}

	public function getRarity() : Rarity{
		return Rarity::uncommon();
	}
}