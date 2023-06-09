<?php

declare(strict_types=1);

namespace skyblock\utils;

use pocketmine\utils\TextFormat;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\items\rarity\Rarity;

class RarityUtils {
	public static function rarityToColour(int $rarity): string
	{
		switch($rarity) {
			case ICustomEnchant::RARITY_UNCOMMON:
				return TextFormat::GRAY;
			case ICustomEnchant::RARITY_ELITE:
				return TextFormat::GREEN;
			case ICustomEnchant::RARITY_RARE:
				return TextFormat::AQUA;
			case ICustomEnchant::RARITY_LEGENDARY:
				return TextFormat::LIGHT_PURPLE;
			case ICustomEnchant::RARITY_MASTERY:
				return TextFormat::DARK_RED;
			case ICustomEnchant::RARITY_HEROIC:
				return TextFormat::GOLD;
			default:
				return "";
		}
	}
	
	public static function tierToRarity(int $tier): ?Rarity {
		return match ($tier) {
			1 => Rarity::common(),
			4 => Rarity::rare(),
			7 => Rarity::epic(),
			10 => Rarity::legendary(),
			13 => Rarity::heroic(),
			16 => Rarity::mastery(),
			19 => Rarity::essence(),
			default => new Rarity("unknown", "Unknown (Error)", "§c§l", 127)
		};
	}

}