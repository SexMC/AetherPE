<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\block\BlockToolType;
use pocketmine\item\Hoe;
use skyblock\items\SkyblockTool;

//TODO: make this function like normal pm hoes
class SkyBlockHoe extends SkyblockTool{

	public function getBlockToolType() : int{
		return BlockToolType::HOE;
	}
}