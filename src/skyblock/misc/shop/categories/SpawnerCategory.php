<?php

declare(strict_types=1);

namespace skyblock\misc\shop\categories;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use skyblock\items\lootbox\types\SpawnerLootbox;
use skyblock\items\special\types\SpawnerItem;
use skyblock\misc\shop\ShopCategory;
use skyblock\misc\shop\ShopItem;

class SpawnerCategory extends ShopCategory {
	public function buildCategoryItem() : Item{
		return VanillaBlocks::MONSTER_SPAWNER()->asItem();
	}

	public function buildItems() : array{
		return [
			new ShopItem(SpawnerItem::getItem(EntityIds::CHICKEN), 23500),
			new ShopItem(SpawnerItem::getItem(EntityIds::COW), 36500),
			new ShopItem(SpawnerItem::getItem(EntityIds::ZOMBIE), 80000),
			new ShopItem(SpawnerItem::getItem(EntityIds::SKELETON), 90000),
			new ShopItem(SpawnerItem::getItem(EntityIds::BLAZE), 360000),
			new ShopItem(SpawnerItem::getItem(EntityIds::SLIME), 560000),
			new ShopItem(SpawnerItem::getItem(EntityIds::IRON_GOLEM), 725000),
			new ShopItem(SpawnerItem::getItem(EntityIds::ZOMBIE_PIGMAN), 1250000),
			new ShopItem(SpawnerItem::getItem(EntityIds::MAGMA_CUBE), 1725000),
			new ShopItem(SpawnerItem::getItem(EntityIds::GUARDIAN), 2325000),
			new ShopItem(SpawnerItem::getItem(EntityIds::TURTLE), 3325000),
			//new ShopItem(SpawnerLootbox::getItem(), 1500000),
		];
	}

	public function getCategoryName(){
		return "Spawner";
	}
}