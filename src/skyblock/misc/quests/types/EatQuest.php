<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types;

use Closure;
use pocketmine\block\Block;
use pocketmine\item\Item;
use skyblock\misc\quests\Quest;

class EatQuest extends Quest {

	public function __construct(private Item $item, int $goal, string $name, string $objective, string $rewardsText, array $rewards = [], ?Closure $onFinish = null){ parent::__construct($goal, $name, $objective, $rewardsText, $rewards, $onFinish); }

	public function getType() : int{
		return self::EAT;
	}

	/**
	 * @param Item $object
	 *
	 * @return int
	 */
	public function shouldIncreaseProgress($object) : int{
		return (int) $object->equals($this->item, true, false);
	}
}