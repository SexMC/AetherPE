<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;


use pocketmine\utils\TextFormat;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandSpawnersUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "spawner";
	}

	public function getName() : string{
		return "Island Spawners";
	}

	public function buildLevels() : array{
		$arr = [];

		for($i = 1; $i <= 10; $i++){
			$cost = match ($i){
				1 => 1000000,
				2 => 60000000,
				3 => 105000000,
				4 => 525000000,
				5 => 825000000,
				6 => 1500000000,
				7 => 1500000000 * 2,
				8 => 1500000000 * 3,
				9 => 1500000000 * 4,
				10 => 1500000000 * 5,
				11 => 1500000000 * 6,
				12 => 1500000000 * 7,
			};
			$regular = 100 * $i;
			$heroic = 50 * $i;
			$add = 25;
			$ess = 0;
			if($i >= 7){
				$ess = 750000 * $i - 6;
			}

			$arr[] = new IslandUpgradeLevel($i, "+$add Spawner Limit", $cost, $regular, $heroic, $ess);
		}

		return $arr;
	}

	public function getMenuColor() : string{
		return TextFormat::MINECOIN_GOLD;
	}

	public function getDescription() : array{
		return [
			"Have you reached the spawner limit?",
			"Well, you can increase the spawner",
			"limit with this upgrade."
		];
	}
}