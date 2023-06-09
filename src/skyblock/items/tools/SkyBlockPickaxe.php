<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\block\BlockToolType;
use pocketmine\item\ToolTier;
use skyblock\items\SkyblockTool;

class SkyBlockPickaxe extends SkyblockTool{

	public function getBlockToolType() : int{
		return BlockToolType::PICKAXE;
	}

	public function getBlockToolHarvestLevel() : int{
		return 1;
	}
}