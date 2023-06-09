<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades;

class IslandUpgradeLevel {

	public function __construct(
		public int $level,
		public string $description,
		public int $moneyCost,
		public int $regularQuestTokenCost,
		public int $heroicQuestTokenCost,
		public int $essence = 0,
	){ }
}