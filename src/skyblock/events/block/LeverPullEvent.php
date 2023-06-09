<?php

declare(strict_types=1);

namespace skyblock\events\block;

use pocketmine\block\Block;
use pocketmine\event\block\BlockEvent;
use pocketmine\player\Player;

class LeverPullEvent extends BlockEvent {

	public function __construct(Block $block, private Player $player){
		parent::__construct($block);
	}


	public function getPlayer() : Player{
		return $this->player;
	}
}