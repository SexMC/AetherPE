<?php

declare(strict_types=1);

namespace skyblock\items\misc\storagesack;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

class FishingStorageSack extends StorageSack {

	public function buildStorageList() : array{
		return [
			VanillaItems::CLAY(),
			VanillaItems::INK_SAC(),
			VanillaBlocks::LILY_PAD()->asItem(),
			VanillaItems::RAW_FISH(),
			VanillaItems::CLOWNFISH(),
			VanillaItems::PUFFERFISH(),
			VanillaItems::PRISMARINE_CRYSTALS(),
			VanillaItems::PRISMARINE_SHARD(),
			VanillaBlocks::SPONGE()->asItem(),
		];
	}

	public function getTypeName() : string{
		return "Fishing";
	}
}