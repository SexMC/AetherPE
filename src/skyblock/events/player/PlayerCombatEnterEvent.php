<?php

declare(strict_types=1);

namespace skyblock\events\player;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerCombatEnterEvent extends PlayerEvent {

	public function __construct(Player $player, private int $combatTime){
		$this->player = $player;
	}

	/**
	 * @return int
	 */
	public function getCombatTime() : int{
		return $this->combatTime;
	}

	/**
	 * @param int $combatTime
	 */
	public function setCombatTime(int $combatTime) : void{
		$this->combatTime = $combatTime;
	}
}