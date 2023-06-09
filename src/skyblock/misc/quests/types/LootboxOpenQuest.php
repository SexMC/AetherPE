<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types;

use Closure;
use pocketmine\block\Block;
use skyblock\items\lootbox\Lootbox;
use skyblock\misc\quests\Quest;

class LootboxOpenQuest extends Quest {

	public function __construct(private string $lb, int $goal, string $name, string $objective, string $rewardsText, array $rewards = [], ?Closure $onFinish = null){ parent::__construct($goal, $name, $objective, $rewardsText, $rewards, $onFinish); }

	public function getType() : int{
		return self::OPEN_LOOTBOX;
	}

	public function shouldIncreaseProgress($object) : int{
		if($object instanceof Lootbox && strtolower($object::getName()) === strtolower($this->lb)){
			return 1;
		}

		return 0;
	}
}