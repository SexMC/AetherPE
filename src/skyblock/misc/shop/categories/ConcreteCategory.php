<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class ConcreteCategory extends ShopCategory {
	public function getCategoryName(){
		return "Concrete";
	}

	public function buildCategoryItem() : Item{
		return VanillaBlocks::CONCRETE()->asItem();
	}

	public function buildItems() : array{
		$concrete = array_map(fn(int $meta) => new ShopItem(BlockFactory::getInstance()->get(BlockLegacyIds::CONCRETE, $meta)->asItem(), 1000), range(0, 15));
		$concretePowder = array_map(fn(int $meta) => new ShopItem(BlockFactory::getInstance()->get(BlockLegacyIds::CONCRETE_POWDER, $meta)->asItem(), 1000), range(0, 15));

		return array_merge($concrete, $concretePowder);
	}
}