<?php

declare(strict_types=1);

namespace skyblock\misc\booster;

use JetBrains\PhpStorm\Pure;
use JsonSerializable;

class Booster implements JsonSerializable{

	public function __construct(private float $boost, private int $currentDuration, private int $originalDuration, private int $startTime){ }

	/**
	 * @return float
	 */
	public function getBoost() : float{
		return round($this->boost, 1);
	}

	/**
	 * @param float $boost
	 */
	public function setBoost(float $boost) : void{
		$this->boost = $boost;
	}

	/**
	 * @return int
	 */
	public function getCurrentDuration() : int{
		return $this->currentDuration;
	}

	/**
	 * @param int $currentDuration
	 */
	public function setCurrentDuration(int $currentDuration) : void{
		$this->currentDuration = $currentDuration;
	}

	/**
	 * @return int
	 */
	public function getOriginalDuration() : int{
		return $this->originalDuration;
	}

	/**
	 * @param int $originalDuration
	 */
	public function setOriginalDuration(int $originalDuration) : void{
		$this->originalDuration = $originalDuration;
	}

	/**
	 * @return int
	 */
	public function getStartTime() : int{
		return $this->startTime;
	}

	/**
	 * @param int $startTime
	 */
	public function setStartTime(int $startTime) : void{
		$this->startTime = $startTime;
	}



	public function jsonSerialize(){
		return [
			$this->boost,
			$this->currentDuration,
			$this->originalDuration,
			$this->startTime,
		];
	}


	public static function jsonDeserialize(array $data): self {
		return new Booster(...$data);
	}
}