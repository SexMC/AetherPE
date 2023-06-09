<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\block\BlockToolType;
use skyblock\items\SkyblockTool;

class SkyBlockShovel extends SkyblockTool{

	public function getBlockToolType() : int{
		return BlockToolType::SHOVEL;
	}
}