<?php

declare(strict_types=1);

namespace skyblock\events\skills;

use pocketmine\event\Event;
use pocketmine\event\player\PlayerEvent;
use skyblock\misc\skills\Skill;
use skyblock\player\AetherPlayer;

class SkillLevelupEvent extends Event {

	public function __construct(
		private AetherPlayer $player,
		private Skill $skill,
		private int $oldLevel,
		private int $newLevel,
	){ }


	/**
	 * @return AetherPlayer
	 */
	public function getPlayer() : AetherPlayer{
		return $this->player;
	}

	/**
	 * @return int
	 */
	public function getNewLevel() : int{
		return $this->newLevel;
	}

	/**
	 * @return int
	 */
	public function getOldLevel() : int{
		return $this->oldLevel;
	}

	/**
	 * @return Skill
	 */
	public function getSkill() : Skill{
		return $this->skill;
	}
}