<?php

declare(strict_types=1);

namespace skyblock\items\misc\storagesack;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

class AgronomyStorageSack extends StorageSack {

	public function buildStorageList() : array{
		return [
			VanillaBlocks::BROWN_MUSHROOM()->asItem(),
			VanillaBlocks::RED_MUSHROOM()->asItem(),
			VanillaBlocks::CACTUS()->asItem(),
			VanillaItems::COCOA_BEANS(),
			VanillaBlocks::MELON()->asItem(),
			VanillaBlocks::PUMPKIN()->asItem(),
			VanillaBlocks::NETHER_WART()->asItem(),
			VanillaBlocks::SUGARCANE()->asItem(),
			VanillaItems::POTATO(),
			VanillaItems::WHEAT(),
			VanillaItems::WHEAT_SEEDS(),
		];
	}

	public function getTypeName() : string{
		return "Agronomy";
	}
}