<?php

declare(strict_types=1);

namespace skyblock\utils;

use skyblock\items\itemattribute\ItemAttributeHolder;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\PvEItemEditor;
use skyblock\items\SkyblockItem;
use skyblock\player\CachedPlayerPveData;

class PveUtils {

	public static function getStrength(): string {
		return "§c" . self::getStrengthSymbol() . " Strength";
	}

	public static function getIntelligence(): string {
		return "§b" . self::getIntelligenceSymbol() . " Intelligence";
	}

	public static function getDefense(): string {
		return "§a" . self::getDefenseSymbol() . "  Defense";
	}

	public static function getDamage(): string {
		return "§c" . self::getDamageSymbol() . " Damage";
	}

	public static function getSpeed(): string {
		return "§f" . self::getSpeedSymbol() . " Speed";
	}

	public static function getCritDamage(): string {
		return "§6" . self::getCritDamageSymbol() . " Critical Damage";
	}

	public static function getCritChance(): string {
		return "§6" . self::getCritChanceSymbol() . " Crit Chance";
	}

	public static function getHealth(): string {
		return "§c" . self::getHealthSymbol() . "  Health";
	}

	public static function getMiningSpeed(): string {
		return "§g" . "Mining Speed";
	}

	public static function getMiningWisdom(): string {
		return "§3" . " Mining Wisdom";
	}


	public static function getColor(string $s){
		return mb_substr($s, 0, 2);
	}


	//SYMBOLS

	public static function getMiningSpeedSymbol(): string {
		return "";
	}

	public static function getDamageSymbol(): string {
		return mb_chr(0xE100);
	}


	public static function getHealthSymbol(): string {
		return mb_chr(0xE1E8);
	}

	public static function getCritChanceSymbol(): string {
		return mb_chr(0xE1EB);
	}

	public static function getCritDamageSymbol(): string {
		return mb_chr(0xE1EB);
	}

	public static function getSpeedSymbol(): string {
		return mb_chr(0xE1F4);
	}

	public static function getDefenseSymbol(): string {
		return mb_chr(0xE1E9);
	}

	public static function getIntelligenceSymbol(): string {
		return mb_chr(0xE1F5);
	}

	public static function getStrengthSymbol(): string {
		return mb_chr(0xE1F3);
	}


	public static function getFinalMagicDamage(float $baseMagicDamage, CachedPlayerPveData $data): float {
		return ($baseMagicDamage + $data->getMagicDamage()) * (1 + ($data->getIntelligence() / 100));
	}

	public static function getItemDamage(ItemAttributeHolder $item): float {
		$damage = $item->getItemAttribute(SkyBlockItemAttributes::DAMAGE())->getValue();
		$str = $item->getItemAttribute(SkyBlockItemAttributes::STRENGTH())->getValue();

		$calculation = ($damage + 5) * (1 + ($str / 100));

		return $calculation;
	}
}