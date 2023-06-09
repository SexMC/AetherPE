<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class WoodCategory extends ShopCategory {
	public function buildCategoryItem() : Item{
		return VanillaBlocks::OAK_LOG()->asItem();
	}

	public function buildItems() : array{
		return [
			new ShopItem(VanillaBlocks::ACACIA_LOG()->asItem(), 150),
			new ShopItem(VanillaBlocks::BIRCH_LOG()->asItem(), 150),
			new ShopItem(VanillaBlocks::DARK_OAK_LOG()->asItem(), 150),
			new ShopItem(VanillaBlocks::JUNGLE_LOG()->asItem(), 150),
			new ShopItem(VanillaBlocks::OAK_LOG()->asItem(), 150),
			new ShopItem(VanillaBlocks::SPRUCE_LOG()->asItem(), 150),

			new ShopItem(VanillaBlocks::ACACIA_LEAVES()->asItem(), 15),
			new ShopItem(VanillaBlocks::BIRCH_LEAVES()->asItem(), 15),
			new ShopItem(VanillaBlocks::DARK_OAK_LEAVES()->asItem(), 15),
			new ShopItem(VanillaBlocks::JUNGLE_LEAVES()->asItem(), 15),
			new ShopItem(VanillaBlocks::ACACIA_LEAVES()->asItem(), 15),
			new ShopItem(VanillaBlocks::OAK_LEAVES()->asItem(), 15),
		];
	}

	public function getCategoryName(){
		return "Wood";
	}
}