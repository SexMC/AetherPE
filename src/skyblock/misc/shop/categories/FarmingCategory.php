<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class FarmingCategory extends ShopCategory {
	public function buildCategoryItem() : Item{
		return VanillaItems::WHEAT_SEEDS();
	}

	public function buildItems() : array{
		return [
			new ShopItem(VanillaItems::BEETROOT_SEEDS(), 100),
			new ShopItem(VanillaItems::MELON_SEEDS(), 100),
			new ShopItem(VanillaItems::PUMPKIN_SEEDS(), 100),
			new ShopItem(VanillaItems::WHEAT_SEEDS(), 100),
			new ShopItem(VanillaItems::POTATO(), 100),
			new ShopItem(VanillaItems::CARROT(), 100),
			new ShopItem(VanillaBlocks::CACTUS()->asItem(), 100),
			new ShopItem(VanillaBlocks::NETHER_WART()->asItem(), 100),
			new ShopItem(VanillaBlocks::SUGARCANE()->asItem(), 100),

			//new ShopItem(VanillaBlocks::SPRUCE_SAPLING()->asItem(), 1000),
			//new ShopItem(VanillaBlocks::ACACIA_SAPLING()->asItem(), 1000),
			//new ShopItem(VanillaBlocks::BAMBOO_SAPLING()->asItem(), 1000),
			//new ShopItem(VanillaBlocks::BIRCH_SAPLING()->asItem(), 1000),
			//new ShopItem(VanillaBlocks::DARK_OAK_SAPLING()->asItem(), 1000),
			//new ShopItem(VanillaBlocks::OAK_SAPLING()->asItem(), 1000),
			//new ShopItem(VanillaBlocks::JUNGLE_SAPLING()->asItem(), 1000),
		];
	}

	public function getCategoryName(){
		return "Farming";
	}
}