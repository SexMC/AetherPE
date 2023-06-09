<?php

declare(strict_types=1);

namespace skyblock\events\player;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

//The amount of ticks it will take for the player to catch something is calculated with this formula: {\displaystyle Ticks=({\text{random number between }}400{\text{ and }}(400-10\times LureEnchantmentTier))\times FishingSpeedMultiplier}
class PlayerPreFishEvent extends PlayerEvent {

	protected array $add = [];


	public function __construct(Player $player, private int $originalFishTime, private float $fishingSpeedMultiplier){
		$this->player = $player;
	}

	public function getOriginalFishTime() : int{
		return $this->originalFishTime;
	}

	public function setOriginalFishTime(int $originalFishTime) : void{
		$this->originalFishTime = $originalFishTime;
	}

	public function getFishingSpeedMultiplier() : float{
		return $this->fishingSpeedMultiplier;
	}

	public function setFishingSpeedMultiplier(float $fishingSpeedMultiplier) : void{
		$this->fishingSpeedMultiplier = $fishingSpeedMultiplier;
	}
}