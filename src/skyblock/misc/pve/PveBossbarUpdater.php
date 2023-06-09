<?php

declare(strict_types=1);

namespace skyblock\misc\pve;

use skyblock\misc\skills\CombatSkill;
use skyblock\misc\skills\FarmingSkill;
use skyblock\misc\skills\ForagingSkill;
use skyblock\misc\skills\MiningSkill;
use skyblock\misc\skills\Skill;
use skyblock\player\AetherPlayer;
use xenialdan\apibossbar\BossBar;

class PveBossbarUpdater {

	public const TYPE_FARMING_SKILL = 1;
	public const TYPE_COMBAT_SKILL = 2;
	public const TYPE_MINING_SKILL = 3;
	public const TYPE_FORAGING_SKILL = 4;

	private int $type = -1;

	private BossBar $bossBar;

	public function __construct(private AetherPlayer $player){
		$this->bossBar = new BossBar();

		$this->bossBar->addPlayer($this->player);


		$this->setType(self::TYPE_FARMING_SKILL);
	}

	public function updateBossBar(): void {
		$skill = $this->getCurrentSkill();
		$xp = number_format($this->player->getSkillData()->getSkillXp($skill::id()));
		$needed = number_format($skill->getXpForLevel($this->player->getSkillData()->getSkillLevel($skill::id()) + 1));

		$percentage = number_format($rawPercentage = $this->player->getSkillData()->getNextLevelPercentage($skill::id()), 2);

		$text = "     §c{$xp}§7/§a{$needed}  §r§7(§c{$percentage}%%%%§7)";
		if($this->player->getSkillData()->getSkillLevel($skill::id()) >= $skill->getMaxLevel()){
			$text = "     §b§lMax level";
		}

		$this->bossBar->setSubTitle($text);
		$this->bossBar->setPercentage($rawPercentage / 100);
	}
	
	public function getTitle(): string {
		return match($this->type){
			self::TYPE_FARMING_SKILL => "§l§aFarming Skill: §d" . number_format($this->player->getSkillData()->getSkillLevel(FarmingSkill::id())),
			self::TYPE_COMBAT_SKILL => "§l§cCombat Skill: §d" . number_format($this->player->getSkillData()->getSkillLevel(CombatSkill::id())),
			self::TYPE_MINING_SKILL => "§l§fMining Skill: §d" . number_format($this->player->getSkillData()->getSkillLevel(MiningSkill::id())),
			self::TYPE_FORAGING_SKILL => "§l§6Foraging Skill: §d" . number_format($this->player->getSkillData()->getSkillLevel(ForagingSkill::id())),
		};
	}

	public function getCurrentSkill(): Skill {
		return match($this->type) {
			self::TYPE_MINING_SKILL => new MiningSkill(),
			self::TYPE_COMBAT_SKILL => new CombatSkill(),
			self::TYPE_FARMING_SKILL => new FarmingSkill(),
			self::TYPE_FORAGING_SKILL => new ForagingSkill(),
		};
	}


	/**
	 * @return int
	 */
	public function getType() : int{
		return $this->type;
	}

	/**
	 * @param int $type
	 */
	public function setType(int $type) : void{
		if($this->type === $type) {
			$this->updateBossBar();
			return;
		}

		$this->type = $type;
		
		$this->updateTitle();
		$this->updateBossBar();
	}

	public function updateTitle(): void {
		$this->bossBar->setTitle($this->getTitle());
	}
}