<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;


use pocketmine\utils\TextFormat;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandMemberUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "members";
	}

	public function getName() : string{
		return "Island Member Limit";
	}

	public function buildLevels() : array{
		return [
			new IslandUpgradeLevel(1, "+1 Island Member Limit", 25000000, 50, 0),
			new IslandUpgradeLevel(2, "+2 Island Member Limit", 75000000, 125, 0),
			new IslandUpgradeLevel(3, "+3 Island Member Limit", 150000000, 150, 0),
			new IslandUpgradeLevel(4, "+4 Island Member Limit", 200000000, 175, 25),
		];
	}

	public function getMenuColor() : string{
		return TextFormat::YELLOW;
	}

	public function getDescription() : array{
		return [
			"Increase Island Member count!"
		];
	}
}