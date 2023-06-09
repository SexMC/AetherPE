<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\items\special\types\ChunkChestItem;
use skyblock\items\vanilla\CustomFishingRod;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class UtilityCategory extends ShopCategory {
	public function buildCategoryItem() : Item{
		return VanillaBlocks::HOPPER()->asItem();
	}

	public function buildItems() : array{
		return [
			new ShopItem(VanillaItems::LAVA_BUCKET(), 500),
			new ShopItem(VanillaItems::WATER_BUCKET(), 100),
			new ShopItem(VanillaBlocks::TORCH()->asItem(), 30),
			new ShopItem(VanillaBlocks::COBWEB()->asItem(), 1500),
			new ShopItem(VanillaBlocks::HOPPER()->asItem(), 10000),
			//new ShopItem(ChunkChestItem::getItem(), 1000000),
			new ShopItem(VanillaItems::FLINT_AND_STEEL(), 3000),
			new ShopItem(VanillaItems::ENDER_PEARL(), 50),
			new ShopItem(VanillaBlocks::ENDER_CHEST()->asItem(), 800),
			new ShopItem(CustomFishingRod::getItem(1), 1000),
			new ShopItem(VanillaItems::BONE(), 1000),
			new ShopItem(VanillaItems::ARROW(), 100),
		];
	}

	public function getCategoryName(){
		return "Utility";
	}
}