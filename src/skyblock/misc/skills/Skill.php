<?php

declare(strict_types=1);

namespace skyblock\misc\skills;

use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\events\skills\SkillLevelupEvent;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

abstract class Skill implements ISkill{

	public function getXpForLevel(int $level): int {
		return self::LEVELS[$level] ?? 99999999999999999;
	}

	public function getEssenceGain(int $level): int {
		if($level <= 2) {
			return $level * 25;
		}

		if($level <= 18){
			return $level * 100;
		}

		if($level <= 24) {
			return $level * 200;
		}

		if($level <= 31) {
			return $level * 415;
		}

		if($level <= 43) {
			return $level * 895;
		}

		return $level * 2694;
	}

	public function getMaxLevel(): int {
		return 50;
	}

	public function onInternalLevelUp(AetherPlayer $player, Session $session, int $old, int $new): void {
		(new SkillLevelupEvent($player, $this, $old, $new))->call();

		$this->onLevelUp($player, $session, $old, $new);
	}

	public function onRegister(): void {}

	abstract protected function onLevelUp(AetherPlayer $player, Session $session, int $old, int $new): void;
	public abstract static function id(): string;
	public abstract function getMenuItemEvery5Levels(): Item;
	public abstract function getMenuLore(AetherPlayer $player, int $level): array;
	public abstract function getBaseItem(AetherPlayer $player): Item;
}