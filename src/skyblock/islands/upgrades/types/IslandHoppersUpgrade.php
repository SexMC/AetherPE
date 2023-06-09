<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;


use cosmicpe\floatingtext\world\WorldManager;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandHoppersUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "hoppers";
	}

	public function getName() : string{
		return "Island Hoppers";
	}

	public function buildLevels() : array{
		$arr = [];

		for($i = 1; $i <= 6; $i++){
			$cost = match ($i){
				1 => 1000000,
				2 => 10000000,
				3 => 55000000,
				4 => 100000000,
				5 => 325000000,
				6 => 550000000,
			};
			$regular = 95 * $i;
			$heroic = 45 * $i;
			$add = 20;

			$arr[] = new IslandUpgradeLevel($i, "+$add Hopper Limit", $cost, $regular, $heroic);
		}

		return $arr;
	}

	public function getMenuColor() : string{
		return TextFormat::GOLD;
	}

	public function getDescription() : array{
		return [
			"Have you reached the hopper limit?",
			"Well, you can increase the hopper",
			"limit with this upgrade."
		];
	}
}