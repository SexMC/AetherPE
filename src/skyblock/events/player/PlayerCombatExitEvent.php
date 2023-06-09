<?php

declare(strict_types=1);

namespace skyblock\events\player;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerCombatExitEvent extends PlayerEvent {

	public function __construct(Player $player){
		$this->player = $player;
	}
}