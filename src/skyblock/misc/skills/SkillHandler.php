<?php

declare(strict_types=1);

namespace skyblock\misc\skills;

use skyblock\traits\AetherHandlerTrait;

class SkillHandler{



	use AetherHandlerTrait;

	/** @var Skill[] */
	private array $skills = [];

	private array $skillStats = [];

	public function onEnable() : void{
		//register

		$this->registerSkill(new CombatSkill());
		$this->registerSkill(new MiningSkill());
		$this->registerSkill(new FarmingSkill());
		$this->registerSkill(new FishingSkill());
		$this->registerSkill(new ForagingSkill());

		$this->registerSkillStat(ISkill::SKILL_STAT_EXTRA_DAMAGE);
	}

	public function registerSkill(Skill $skill): void {
		$this->skills[$skill::id()] = $skill;

		$skill->onRegister();
	}

	public function registerSkillStat(string $skill): void {
		$this->skillStats[] = $skill;
	}

	public function getSkill(string $id): ?Skill {
		return $this->skills[$id];
	}

	/**
	 * @return Skill[]
	 */
	public function getSkills() : array{
		return $this->skills;
	}

	/**
	 * @return array
	 */
	public function getSkillStats() : array{
		return $this->skillStats;
	}
}