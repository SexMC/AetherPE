<?php

declare(strict_types=1);

namespace skyblock\misc\pve\fishing;

class HotspotBoost {

	public function __construct(
		public int $fasterFishingInTicks,
		public float $extraFishingSkillXP,
		public float $extraSeaBossSpawnEggChance,
		public float $extraTreasureLootChance,
	){ }
}