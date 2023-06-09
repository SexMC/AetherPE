<?php

declare(strict_types=1);

namespace skyblock\events\player;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;
use skyblock\items\special\types\fishing\Bait;

class PlayerBaitConsumeEvent extends PlayerEvent {

	public function __construct(Player $player, private Bait $bait, private bool $shouldConsume = true){
		$this->player = $player;
	}


	/**
	 * @param bool $shouldConsume
	 */
	public function setShouldConsume(bool $shouldConsume) : void{
		$this->shouldConsume = $shouldConsume;
	}

	/**
	 * @return bool
	 */
	public function isShouldConsume() : bool{
		return $this->shouldConsume;
	}

	/**
	 * @return Bait
	 */
	public function getBait() : Bait{
		return $this->bait;
	}
}