<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;

use pocketmine\utils\TextFormat;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandWarzoneControlUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "wzc";
	}

	public function getName() : string{
		return "Stronghold Power";
	}

	public function buildLevels() : array{
		return [
			new IslandUpgradeLevel(1, "+5 Bonus Power from Warzone's Stronghold", 50000000, 100, 50),
			new IslandUpgradeLevel(2, "+10 Bonus Power from Warzone's Stronghold", 100000000, 150, 100),
			new IslandUpgradeLevel(3, "+15 Bonus Power from Warzone's Stronghold", 175000000, 225, 150),
		];
	}

	public function getMenuColor() : string{
		return TextFormat::DARK_AQUA;
	}

	public function getDescription() : array{
		return [
			"Increased Power gain from the Stronghold",
			"in the Warzone Warp.",
		];
	}
}