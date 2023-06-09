<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class PotionCategory extends ShopCategory {
	public function buildCategoryItem() : Item{
		return VanillaItems::GLASS_BOTTLE();
	}

	public function buildItems() : array{
		return [
			new ShopItem(VanillaItems::STRONG_HEALING_SPLASH_POTION(), 500),
			new ShopItem(VanillaItems::STRONG_SWIFTNESS_POTION(), 500),
			new ShopItem(VanillaItems::STRONG_STRENGTH_POTION(), 500),
			new ShopItem(VanillaItems::WATER_BREATHING_POTION(), 500),
			new ShopItem(VanillaItems::FIRE_RESISTANCE_POTION(), 500),
			new ShopItem(VanillaItems::STRONG_REGENERATION_POTION(), 500),
		];
	}

	public function getCategoryName(){
		return "Potion";
	}
}