<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class WoolCategory extends ShopCategory {
	public function getCategoryName(){
		return "Wool";
	}

	public function buildCategoryItem() : Item{
		return VanillaBlocks::WOOL()->asItem();
	}

	public function buildItems() : array{
		return array_map(fn(int $meta) => new ShopItem(BlockFactory::getInstance()->get(BlockLegacyIds::WOOL, $meta)->asItem(), 50), range(0, 15));
	}
}