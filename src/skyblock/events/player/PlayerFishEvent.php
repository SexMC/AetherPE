<?php

declare(strict_types=1);

namespace skyblock\events\player;

use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;
use skyblock\entity\projectile\FishingRodEntity;

class PlayerFishEvent extends PlayerEvent {

	protected array $add = [];


	public function __construct(Player $player, private FishingRodEntity $fishingHook, private int $fishingSkillXp, private array $rewards){
		$this->player = $player;
	}

	/**
	 * @return FishingRodEntity
	 */
	public function getFishingHook() : FishingRodEntity{
		return $this->fishingHook;
	}


	/**
	 * @return array
	 */
	public function getRewards() : array{
		return $this->rewards;
	}

	/**
	 * @return int
	 */
	public function getFishingSkillXp() : int{
		return $this->fishingSkillXp;
	}

	public function getFinalTotalFishingXP(): int {
		return array_sum($this->add) + $this->fishingSkillXp;
	}

	public function addFishingXP(int $amount, string $reason): void {
		$this->add[$reason] = $amount;
	}

	/**
	 * @param array $rewards
	 */
	public function setRewards(array $rewards) : void{
		$this->rewards = $rewards;
	}
}