<?php

declare(strict_types=1);

namespace skyblock\items\misc\storagesack;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

class CombatStorageSack extends StorageSack {

	public function buildStorageList() : array{
		return [
			VanillaItems::BLAZE_ROD(),
			VanillaItems::BONE(),
			VanillaItems::ENDER_PEARL(),
			VanillaItems::GHAST_TEAR(),
			VanillaItems::GUNPOWDER(),
			VanillaItems::MAGMA_CREAM(),
			VanillaItems::ROTTEN_FLESH(),
			VanillaItems::SLIMEBALL(),
			VanillaItems::SPIDER_EYE(),
			VanillaItems::STRING(),
		];
	}

	public function getTypeName() : string{
		return "Combat";
	}
}