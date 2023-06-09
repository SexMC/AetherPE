<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;


use pocketmine\utils\TextFormat;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandPersonalBoosterUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "boost";
	}

	public function getName() : string{
		return "Island Personal Booster";
	}

	public function buildLevels() : array{
		return [
			new IslandUpgradeLevel(1, "1.5x Island Farming EXP Booster\n§r  §7(Total of 1.5x Bonus Multiplier when maxed)", 1500000000, 250, 350),
		];
	}

	public function getMenuColor() : string{
		return TextFormat::YELLOW;
	}

	public function getDescription() : array{
		return [
			"Lobe your island members? you should give them",
			"some lobe. This island upgrade grants everyone",
			"on the island a bonus farming exp multiplier.",
			"This can be stacked with other multipliers.",
			"This can be stacked with other multipliers.",
			"§cBooster only applies if you're on the ISLAND!"
		];
	}
}