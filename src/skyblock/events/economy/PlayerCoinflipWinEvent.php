<?php

declare(strict_types=1);

namespace skyblock\events\economy;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerCoinflipWinEvent extends PlayerEvent {

	public function __construct(Player $player){
		$this->player = $player;
	}
}