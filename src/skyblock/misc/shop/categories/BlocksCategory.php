<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class BlocksCategory extends ShopCategory {
	public function buildCategoryItem() : Item{
		return VanillaBlocks::STONE_BRICKS()->asItem();
	}

	public function buildItems() : array{
		return [
			new ShopItem(VanillaBlocks::STONE_BRICKS()->asItem(), 1000),
			new ShopItem(VanillaBlocks::MOSSY_STONE_BRICKS()->asItem(), 1000),
			new ShopItem(VanillaBlocks::CRACKED_STONE_BRICKS()->asItem(), 1000),
			new ShopItem(VanillaBlocks::CHISELED_STONE_BRICKS()->asItem(), 1000),
			new ShopItem(VanillaBlocks::END_STONE_BRICKS()->asItem(), 1000),
			new ShopItem(VanillaBlocks::PRISMARINE_BRICKS()->asItem(), 1000),
			new ShopItem(VanillaBlocks::NETHER_BRICKS()->asItem(), 1000),

			new ShopItem(VanillaBlocks::COBBLESTONE()->asItem(), 100),
			new ShopItem(VanillaBlocks::MOSSY_COBBLESTONE()->asItem(), 100),
			new ShopItem(VanillaBlocks::SMOOTH_STONE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::NETHERRACK()->asItem(), 1000),
			new ShopItem(VanillaBlocks::END_STONE()->asItem(), 1000),

			new ShopItem(VanillaBlocks::DIRT()->asItem(), 25),
			new ShopItem(VanillaBlocks::GRASS()->asItem(), 1000),
			new ShopItem(VanillaBlocks::PODZOL()->asItem(), 1000),
			new ShopItem(VanillaBlocks::MYCELIUM()->asItem(), 1000),

			new ShopItem(VanillaBlocks::PURPUR()->asItem(), 1000),
			new ShopItem(VanillaBlocks::PURPUR_PILLAR()->asItem(), 1000),

			new ShopItem(VanillaBlocks::SNOW()->asItem(), 1000),
			new ShopItem(VanillaBlocks::ICE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::PACKED_ICE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::BLUE_ICE()->asItem(), 1000),

			new ShopItem(VanillaBlocks::SAND()->asItem(), 1000),
			new ShopItem(VanillaBlocks::SANDSTONE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::RED_SANDSTONE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::CHISELED_SANDSTONE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::CHISELED_RED_SANDSTONE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::CUT_RED_SANDSTONE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::CUT_SANDSTONE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::SMOOTH_RED_SANDSTONE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::SMOOTH_SANDSTONE()->asItem(), 1000),

			new ShopItem(VanillaBlocks::QUARTZ()->asItem(), 1000),
			new ShopItem(VanillaBlocks::QUARTZ_PILLAR()->asItem(), 1000),
			new ShopItem(VanillaBlocks::CHISELED_QUARTZ()->asItem(), 1000),
			new ShopItem(VanillaBlocks::SMOOTH_QUARTZ()->asItem(), 1000),

			new ShopItem(VanillaBlocks::PRISMARINE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::PRISMARINE_BRICKS()->asItem(), 1000),
			new ShopItem(VanillaBlocks::DARK_PRISMARINE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::SLIME()->asItem(), 5000),
			new ShopItem(VanillaBlocks::MAGMA()->asItem(), 5000),
			new ShopItem(VanillaBlocks::SOUL_SAND()->asItem(), 5000),


			new ShopItem(VanillaBlocks::POLISHED_ANDESITE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::POLISHED_DIORITE()->asItem(), 1000),
			new ShopItem(VanillaBlocks::POLISHED_GRANITE()->asItem(), 1000),

			new ShopItem(VanillaBlocks::CLAY()->asItem(), 1000),
			new ShopItem(VanillaBlocks::HARDENED_CLAY()->asItem(), 1000),
			new ShopItem(VanillaBlocks::STAINED_CLAY()->asItem(), 1000),
			new ShopItem(VanillaBlocks::SEA_LANTERN()->asItem(), 1000),

			new ShopItem(VanillaBlocks::OBSIDIAN()->asItem(), 10000),
			new ShopItem(VanillaBlocks::GLOWSTONE()->asItem(), 10000),


		];
	}

	public function getCategoryName(){
		return "Blocks";
	}
}