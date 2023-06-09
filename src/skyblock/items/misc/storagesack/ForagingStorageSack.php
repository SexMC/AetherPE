<?php

declare(strict_types=1);

namespace skyblock\items\misc\storagesack;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;

class ForagingStorageSack extends StorageSack {

	public function buildStorageList() : array{
		return [
			VanillaBlocks::BIRCH_LOG()->asItem(),
			VanillaBlocks::OAK_LOG()->asItem(),
			VanillaBlocks::JUNGLE_LOG()->asItem(),
			VanillaBlocks::SPRUCE_LOG()->asItem(),
			VanillaBlocks::ACACIA_LOG()->asItem(),
			VanillaBlocks::DARK_OAK_LOG()->asItem(),

			VanillaItems::APPLE(),
			VanillaBlocks::DARK_OAK_SAPLING()->asItem(),
			VanillaBlocks::BIRCH_SAPLING()->asItem(),
			VanillaBlocks::OAK_SAPLING()->asItem(),
			VanillaBlocks::JUNGLE_SAPLING()->asItem(),
			VanillaBlocks::SPRUCE_SAPLING()->asItem(),
			VanillaBlocks::ACACIA_SAPLING()->asItem(),
		];
	}

	public function getTypeName() : string{
		return "Foraging";
	}
}