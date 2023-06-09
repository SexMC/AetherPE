<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class FoodCategory extends ShopCategory {
	public function buildCategoryItem() : Item{
		return VanillaItems::STEAK();
	}

	public function buildItems() : array{
		return [
			new ShopItem(VanillaItems::GOLDEN_APPLE(), 10000),
			new ShopItem(VanillaItems::STEAK(), 1000),
			new ShopItem(VanillaItems::COOKED_PORKCHOP(), 1000),
			new ShopItem(VanillaItems::COOKED_CHICKEN(), 1000),
			new ShopItem(VanillaItems::COOKED_MUTTON(), 1000),
			new ShopItem(VanillaItems::COOKED_FISH(), 1000),
			new ShopItem(VanillaItems::COOKED_RABBIT(), 1000),
			new ShopItem(VanillaItems::COOKED_SALMON(), 1000),
			new ShopItem(VanillaItems::COOKIE(), 250),
			new ShopItem(VanillaItems::BREAD(), 1000),
			new ShopItem(VanillaItems::BAKED_POTATO(), 1000),
			new ShopItem(VanillaItems::PUMPKIN_PIE(), 1000),

		];
	}

	public function getCategoryName(){
		return "Food";
	}
}