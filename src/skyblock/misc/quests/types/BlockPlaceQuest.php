<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types;

use Closure;
use pocketmine\block\Block;
use skyblock\misc\quests\Quest;

class BlockPlaceQuest extends BlockBreakQuest {
	public function getType() : int{
		return self::PLACE_BLOCK;
	}
}