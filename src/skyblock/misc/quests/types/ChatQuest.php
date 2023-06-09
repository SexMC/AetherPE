<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types;

use Closure;
use pocketmine\block\Block;
use skyblock\misc\quests\Quest;

class ChatQuest extends Quest {

	public function __construct(private string $msg, int $goal, string $name, string $objective, string $rewardsText, array $rewards = [], ?Closure $onFinish = null){ parent::__construct($goal, $name, $objective, $rewardsText, $rewards, $onFinish); }

	public function getType() : int{
		return self::CHAT;
	}

	public function shouldIncreaseProgress($object) : int{
		return (strtolower($object) === strtolower($this->msg)) ? 1 : 0;
	}
}