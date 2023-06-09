<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class TerracottaCategory extends ShopCategory {
	public function getCategoryName(){
		return "Terracotta";
	}

	public function buildCategoryItem() : Item{
		return ItemFactory::getInstance()->get(BlockLegacyIds::TERRACOTTA);
	}

	public function buildItems() : array{
		$concrete = array_map(fn(int $meta) => new ShopItem(BlockFactory::getInstance()->get(BlockLegacyIds::TERRACOTTA, $meta)->asItem(), 1000), range(0, 15));

		$concrete[] = VanillaBlocks::BLACK_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::BLUE_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::BROWN_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::CYAN_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::GRAY_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::GREEN_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::LIGHT_BLUE_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::LIGHT_GRAY_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::LIME_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::MAGENTA_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::ORANGE_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::PINK_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::PURPLE_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::RED_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::WHITE_GLAZED_TERRACOTTA()->asItem();
		$concrete[] = VanillaBlocks::YELLOW_GLAZED_TERRACOTTA()->asItem();

		foreach($concrete as $k => $v){
			if(!$v instanceof ShopItem){
				$concrete[$k] = new ShopItem($v, 5000);
			}
		}

		return $concrete;
	}
}