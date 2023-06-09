<?php

declare(strict_types=1);

namespace skyblock\blocks;

use pocketmine\block\Crops;
use pocketmine\block\Farmland;
use pocketmine\block\VanillaBlocks;

class CustomFarmland extends Farmland {
	public function onRandomTick() : void{
		//farmland will be usable without water
	}

}