<?php

declare(strict_types=1);

namespace skyblock\items\misc\storagesack;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

class MiningStorageSack extends StorageSack {

	public function buildStorageList() : array{
		return [
			VanillaItems::COAL(),
			VanillaBlocks::COBBLESTONE()->asItem(),
			VanillaBlocks::END_STONE()->asItem(),
			VanillaBlocks::GRAVEL()->asItem(),
			VanillaBlocks::NETHERRACK()->asItem(),
			VanillaBlocks::SAND()->asItem(),
			VanillaBlocks::OBSIDIAN()->asItem(),
			VanillaBlocks::STONE()->asItem(),
			VanillaItems::DIAMOND(),
			VanillaItems::EMERALD(),
			VanillaItems::GOLD_INGOT(),
			VanillaItems::GLOWSTONE_DUST(),
			VanillaItems::LAPIS_LAZULI(),
			VanillaItems::IRON_INGOT(),
			VanillaItems::NETHER_QUARTZ(),
			VanillaItems::REDSTONE_DUST(),
		];
	}

	public function getTypeName() : string{
		return "Mining";
	}
}