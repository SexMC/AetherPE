<?php

declare(strict_types=1);

namespace skyblock\entity\minion;

use pocketmine\item\Item;

class MinionLevel {

	public function __construct(private int $level, private float $baseSpeed, private array $needs){ }


	/**
	 * @return float
	 */
	public function getBaseSpeed() : float{
		return $this->baseSpeed;
	}

	/**
	 * @return int
	 */
	public function getLevel() : int{
		return $this->level;
	}

	/**
	 * @return Item[]
	 */
	public function getNeeds() : array{
		return $this->needs;
	}
}