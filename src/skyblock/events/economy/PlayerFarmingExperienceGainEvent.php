<?php

declare(strict_types=1);

namespace skyblock\events\economy;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerFarmingExperienceGainEvent extends PlayerEvent {

	protected array $add = [];

	public function __construct(Player $player, protected int $gain){
		$this->player = $player;
	}

	/**
	 * @return int
	 */
	public function getGain() : int{
		return $this->gain;
	}

	public function addGain(int $amount, string $reason): void {
		$this->add[$reason] = $amount;
	}

	public function getTotalGain(): int {
		return array_sum($this->add) + $this->gain;
	}
}