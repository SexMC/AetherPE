<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades\types;


use pocketmine\utils\TextFormat;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\island\IslandUpdateDataPacket;
use skyblock\islands\Island;
use skyblock\islands\upgrades\IslandUpgrade;
use skyblock\islands\upgrades\IslandUpgradeLevel;

class IslandSizeUpgrade extends IslandUpgrade {
	public static function getIdentifier() : string{
		return "size";
	}

	public function getName() : string{
		return "Island Size";
	}

	public function onUpgrade(Island $island) : void{
		CommunicationLogicHandler::getInstance()->sendPacket(new IslandUpdateDataPacket(
			$island->getName(),
			IslandUpdateDataPacket::UPDATE_BOUNDING_BOX
		));
	}

	public function buildLevels() : array{
		$arr = [];

		for($i = 1; $i <= 6; $i++){
			$cost = match ($i){
				1 => 1000000,
				2 => 20000000,
				3 => 75000000,
				4 => 125000000,
				5 => 425000000,
				6 => 750000000,
			};
			$regular = 100 * $i;
			$heroic = 50 * $i;
			$add = 20;

			$arr[] = new IslandUpgradeLevel($i, "+{$add}x{$add} Island Size", $cost, $regular, $heroic);
		}

		return $arr;
	}

	public function getMenuColor() : string{
		return TextFormat::GOLD;
	}

	public function getDescription() : array{
		return [
			"Do you need more space on your island?",
			"Well, you can increase the size of",
			"your island with this upgrade."
		];
	}
}