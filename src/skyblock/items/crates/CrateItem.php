<?php

declare(strict_types=1);

namespace skyblock\items\crates;

use pocketmine\item\Item;

class CrateItem {

	public function __construct(private Item $item, private int $chance, private int $minCount = 1, private int $maxCount = 1){ }

	/**
	 * @return Item
	 */
	public function getItem() : Item{
		return $this->item->setCount(mt_rand($this->getMinCount(), $this->getMaxCount()));
	}

	/**
	 * @return int
	 */
	public function getChance() : int{
		return $this->chance;
	}

	/**
	 * @return int
	 */
	public function getMinCount() : int{
		return $this->minCount;
	}

	/**
	 * @return int
	 */
	public function getMaxCount() : int{
		return $this->maxCount;
	}


}