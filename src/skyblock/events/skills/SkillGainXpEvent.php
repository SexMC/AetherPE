<?php

declare(strict_types=1);

namespace skyblock\events\skills;

use pocketmine\event\Event;
use pocketmine\event\player\PlayerEvent;
use skyblock\misc\skills\Skill;
use skyblock\player\AetherPlayer;

class SkillGainXpEvent extends Event {

	public function __construct(
		private AetherPlayer $player,
		private Skill $skill,
		private float $xp,
	){ }


	/**
	 * @return AetherPlayer
	 */
	public function getPlayer() : AetherPlayer{
		return $this->player;
	}

	/**
	 * @return float
	 */
	public function getXp() : float{
		return $this->xp;
	}
	/**
	 * @return Skill
	 */
	public function getSkill() : Skill{
		return $this->skill;
	}
}