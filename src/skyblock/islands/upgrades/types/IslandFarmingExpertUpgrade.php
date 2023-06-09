<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;

use pocketmine\utils\TextFormat;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandFarmingExpertUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "farm";
	}

	public function getName() : string{
		return "Farming Expert";
	}

	public function buildLevels() : array{
		return [
			new IslandUpgradeLevel(1, "+5% Chance to replenish a farmed crop.", 50000000, 100, 0),
			new IslandUpgradeLevel(2, "+10% Chance to replenish a farmed crop.", 500000000, 125, 0),
			new IslandUpgradeLevel(3, "+15% Chance to replenish a farmed crop.", 650000000, 150, 0),
			new IslandUpgradeLevel(4, "+20% Chance to replenish a farmed crop.", 750000000, 150, 25),
		];
	}

	public function getMenuColor() : string{
		return TextFormat::DARK_AQUA;
	}

	public function getDescription() : array{
		return [
			"Chance to replenish a crop on it's full form",
			"when farmed."
		];
	}
}