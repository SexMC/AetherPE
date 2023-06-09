<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types;

use Closure;
use pocketmine\block\Block;
use pocketmine\block\Crops;
use pocketmine\item\Item;
use skyblock\misc\quests\Quest;

class SellQuest extends Quest {

	public function __construct(private Item $item, int $goal, string $name, string $objective, string $rewardsText, array $rewards = [], ?Closure $onFinish = null){ parent::__construct($goal, $name, $objective, $rewardsText, $rewards, $onFinish); }

	public function getType() : int{
		return self::SELL;
	}

	public function shouldIncreaseProgress($object) : int{
		$progress = 0;
		/** @var Item $i */
		foreach ($object as $i){
			if($i->equals($this->item, true, false)){
				$progress += $i->getCount();
			}
		}

		return $progress;
	}
}