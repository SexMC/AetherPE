<?php

declare(strict_types=1);

namespace skyblock\player;

use pocketmine\entity\ExperienceManager;
use pocketmine\entity\Human;
use pocketmine\player\Player;
use skyblock\events\economy\PlayerExperienceGainEvent;
use skyblock\utils\ScoreboardUtils;

class AetherExperienceManager extends ExperienceManager {

	private Player $player;

	public function __construct(Human $entity){
		if($entity instanceof Player){
			$this->player = $entity;
		}

		parent::__construct($entity);
	}

	public function setXpAndProgress(?int $level, ?float $progress) : bool{
		$parent = parent::setXpAndProgress($level, $progress);
		ScoreboardUtils::setLine($this->player, ScoreboardUtils::LINE_PERSONAL_XP, null);

		return $parent;
	}


	public function addXp(int $amount, bool $playSound = true, bool $callEvent = true) : bool{
		if($callEvent === true){
			$event = new PlayerExperienceGainEvent($this->player, $amount);
			$event->call();
			if($event->isCancelled()) {
				return false;
			}

			$amount = $event->getGain();
		}

		return parent::addXp((int) $amount, $playSound);
	}
}