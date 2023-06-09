<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types;

use Closure;
use pocketmine\block\Block;
use skyblock\misc\quests\Quest;

class CommandQuest extends Quest {

	public function __construct(private string $command, int $goal, string $name, string $objective, string $rewardsText, array $rewards = [], ?Closure $onFinish = null){ parent::__construct($goal, $name, $objective, $rewardsText, $rewards, $onFinish); }

	public function getType() : int{
		return self::COMMAND;
	}

	public function shouldIncreaseProgress($object) : int{
		return (strtolower($object) === strtolower($this->command)) ? 1 : 0;
	}
}