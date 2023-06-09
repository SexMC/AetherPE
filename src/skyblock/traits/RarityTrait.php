<?php

declare(strict_types=1);

namespace skyblock\traits;

trait RarityTrait {

	protected int $rarity;

	public static function uncommon(): int {
		return 1;
	}

	public static function elite(): int {
		return 3;
	}

	public static function rare(): int {
		return 5;
	}

	public static function legendary(): int {
		return 7;
	}

	public static function mastery(): int {
		return 10;
	}

	public function isMastery(): bool {
		return $this->rarity === self::mastery();
	}

	public function isLegendary(): bool {
		return $this->rarity === self::legendary();
	}

	public function isRare(): bool {
		return $this->rarity === self::rare();
	}

	public function isElite(): bool {
		return $this->rarity === self::elite();
	}

	public function isUncommon(): bool {
		return $this->rarity === self::uncommon();
	}

	/**
	 * @param int $rarity
	 */
	public function setRarity(int $rarity) : void{
		$this->rarity = $rarity;
	}

	/**
	 * @return int
	 */
	public function getRarity() : int{
		return $this->rarity;
	}
}