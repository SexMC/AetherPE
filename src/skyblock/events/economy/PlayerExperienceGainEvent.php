<?php

declare(strict_types=1);

namespace skyblock\events\economy;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerExperienceGainEvent extends PlayerEvent implements Cancellable{
	use CancellableTrait;

	protected float $gain;

	protected float $originalGain;

	public function __construct(Player $player, float $gain){
		$this->player = $player;
		$this->gain = $gain;
		$this->originalGain = $gain;
	}

	/**
	 * @return float
	 */
	public function getOriginalGain() : float{
		return $this->originalGain;
	}

	/**
	 * @return float
	 */
	public function getGain() : float{
		return $this->gain;
	}

	public function addBoost(float $boost): void {
		$this->gain += $this->originalGain * $boost;
	}
}