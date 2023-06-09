<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;

use pocketmine\utils\TextFormat;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandDuplicationTechniqueUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "dupe";
	}

	public function getName() : string{
		return "Duplication Technique";
	}

	public function buildLevels() : array{
		return [
			new IslandUpgradeLevel(1, "1% Chance to activate", 1000000000, 150, 200),
			new IslandUpgradeLevel(2, "2% Chance to activate", 1250000000, 175, 225),
			new IslandUpgradeLevel(3, "3% Chance to activate", 1500000000, 250, 250),
			new IslandUpgradeLevel(4, "4% Chance to activate", 1750000000, 250, 250),
			new IslandUpgradeLevel(5, "5% Chance to activate", 2000000000, 250, 250),
		];
	}

	public function getMenuColor() : string{
		return TextFormat::DARK_RED;
	}

	public function getDescription() : array{
		return [
			"Slight chance to get double the loot",
			"from any stronghold chest."
		];
	}
}