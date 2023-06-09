<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class GlassCategory extends ShopCategory {
	public function getCategoryName(){
		return "Glass";
	}

	public function buildCategoryItem() : Item{
		return VanillaBlocks::GLASS()->asItem();
	}

	public function buildItems() : array{
		$arr = array_map(fn(int $meta) => new ShopItem(BlockFactory::getInstance()->get(BlockLegacyIds::STAINED_GLASS, $meta)->asItem(), 50), range(0, 15));
		$arr[] = new ShopItem(VanillaBlocks::GLASS()->asItem(), 25);

		return $arr;
	}
}