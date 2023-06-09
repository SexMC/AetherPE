<?php

declare(strict_types=1);

namespace skyblock\player;

use pocketmine\block\Crops;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Dye;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\DebugInfoPacket;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\Server;
use pocketmine\world\sound\XpLevelUpSound;
use RedisClient\Pipeline\Version\Pipeline6x0;
use skyblock\Database;
use skyblock\events\skills\SkillGainXpEvent;
use skyblock\events\skills\SkillLevelupEvent;
use skyblock\Main;
use skyblock\misc\skills\CombatSkill;
use skyblock\misc\skills\Skill;
use skyblock\misc\skills\SkillHandler;
use skyblock\sessions\Session;

class CachedPlayerSkillData {

	private array $data = [];

	private array $skillStats = [];

	private string $username;

	public function __construct(string $username){
		$this->username = strtolower($username);

		$result = Database::getInstance()->getRedis()->pipeline(function(Pipeline6x0 $pipeline) {
			foreach(SkillHandler::getInstance()->getSkills() as $skill){
				$id = strtolower($skill::id());

				$pipeline->get("player.{$this->username}.skills.$id.level");
				$pipeline->get("player.{$this->username}.skills.$id.xp");
			}

			foreach(SkillHandler::getInstance()->getSkillStats() as $stat){
				$pipeline->get("player.{$this->username}.skills.$stat");
			}
		});

		$values = array_values(SkillHandler::getInstance()->getSkills());
		$slot = 0;

		foreach($result as $k => $v){ //load skill data
			if($k % 2 == 0){
				if(isset($values[$slot])){
					$this->data[strtolower($values[$slot]::id())] = [(int) $v, (float) ($result[$k+1] ?? 0)];

					$slot++;

					unset($result[$k]);
					unset($result[$k+1]);
				}
			}
		}

		$result = array_values($result);
		foreach($result as $k => $v){ //load skill stat data
			if(isset(SkillHandler::getInstance()->getSkillStats()[$k])){
				$this->skillStats[strtolower(SkillHandler::getInstance()->getSkillStats()[$k])] = (int) ($v ?? 0);
			}
		}


		Main::debug("Loaded skill data ({$this->username})");

	}

	public function save(?Pipeline6x0 $pipeline = null): void {
		if(!empty($this->data)){
			if($pipeline !== null){
				foreach($this->data as $k => $v){
					$pipeline->set("player.{$this->username}.skills.$k.level", $v[0]);
					$pipeline->set("player.{$this->username}.skills.$k.xp", $v[1]);
					Main::debug("Saving $k player skill data ({$this->username})");
				}

				foreach($this->skillStats as $k => $v){
					$pipeline->set("player.{$this->username}.skills.$k", $v);
				}

				return;
			}

			Database::getInstance()->getRedis()->pipeline(function(Pipeline6x0 $pipeline) {
				foreach($this->data as $k => $v){
					$pipeline->set("player.{$this->username}.skills.$k.level", $v[0]);
					$pipeline->set("player.{$this->username}.skills.$k.xp", $v[1]);
					Main::debug("Saving $k player skill data ({$this->username})");
				}
			});

			Main::debug("saved player skill data ({$this->username})");
		}
	}

	public function getSkillStat(string $skill): int {
		return $this->skillStats[strtolower($skill)] ?? 0;
	}

	public function setSkillStat(string $skill, int $value): void {
		$this->skillStats[strtolower($skill)] = $value;
	}

	public function increaseSkillStat(string $skill, int $xp): void {
		if(!isset($this->skillStats[strtolower($skill)][1])){
			$this->skillStats[strtolower($skill)] = 0;
		}

		$this->skillStats[strtolower($skill)] += $xp;
	}


	public function getSkillLevel(string $skill): int {
		return $this->data[strtolower($skill)][0] ?? 0;
	}

	public function getSkillXp(string $skill): float {
		return $this->data[strtolower($skill)][1] ?? 0;
	}

	public function setSkillLevel(string $skill, int $level): void {
		$this->data[strtolower($skill)][0] = $level;
	}

	public function setSkillXp(string $skill, float $xp): void {
		$this->data[strtolower($skill)][1] = $xp;
	}

	public function getNextLevelPercentage(string $skill): float {
		$level = $this->getSkillLevel($skill);

		if($level >= 50) return 100;

		return 100 / (SkillHandler::getInstance()->getSkill($skill)->getXpForLevel($level + 1)) * $this->getSkillXp($skill);
	}

	public function increaseSkillXp(string $skill, float $xp): void {
		if(!isset($this->data[strtolower($skill)][1])){
			$this->data[strtolower($skill)][1] = 0;
		}


		$this->data[strtolower($skill)][1] += $xp;

		$skillClass = SkillHandler::getInstance()->getSkill($skill);


		if($skillClass instanceof Skill){
			$level = $this->getSkillLevel($skill);
			$player = Server::getInstance()->getPlayerExact(substr($this->username, 0, strpos($this->username, "-profile-")));
			(new SkillGainXpEvent($player, $skillClass, $xp))->call();

			if($level >= $skillClass->getMaxLevel()) return;



			if($this->getSkillXp($skill) >= $skillClass->getXpForLevel($level + 1)){
				$s = new Session($this->username);

				if($player === null) return;


				$this->setSkillLevel($skill, $level + 1);
				$this->setSkillXp($skill, 0);

				$skillClass->onInternalLevelUp($player, $s, $level, $level+1);
			}
		}
	}
}