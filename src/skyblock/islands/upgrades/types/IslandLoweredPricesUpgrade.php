<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;

use pocketmine\utils\TextFormat;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandLoweredPricesUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "low";
	}

	public function getName() : string{
		return "Lowered Prices";
	}

	public function buildLevels() : array{
		return [
			new IslandUpgradeLevel(1, "-10% /enchanter EXP prices", 100000000, 100, 50),
			new IslandUpgradeLevel(2, "-25% /enchanter EXP prices", 250000000, 150, 75),
			new IslandUpgradeLevel(3, "-35% /enchanter EXP prices", 350000000, 200, 100),
			new IslandUpgradeLevel(4, "-50% /enchanter EXP prices", 500000000, 250, 125),
		];
	}

	public function getMenuColor() : string{
		return TextFormat::MINECOIN_GOLD;
	}

	public function getDescription() : array{
		return [
			"Reduce /enchanter EXP prices by a",
			"percentage, this only applies on",
			"players on your island."
		];
	}
}