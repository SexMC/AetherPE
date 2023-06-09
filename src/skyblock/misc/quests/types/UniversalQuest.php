<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types;

use Closure;
use skyblock\misc\quests\Quest;

class UniversalQuest extends Quest {

	public function __construct(private int $type, int $goal, string $name, string $objective, string $rewardsText, array $rewards = [], ?Closure $onFinish = null){ parent::__construct($goal, $name, $objective, $rewardsText, $rewards, $onFinish); }

	public function getType() : int{
		return $this->type;
	}

	public function shouldIncreaseProgress($object) : int{
		if(is_int($object)){
			return (int) $object;
		}

		return 1;
	}
}