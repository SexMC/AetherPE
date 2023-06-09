<?php

declare(strict_types=1);

namespace skyblock\misc\collection\foraging;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\accessory\types\WoodAffinityTalismanAccessory;
use skyblock\items\armor\leaflet\LeafletSet;
use skyblock\items\misc\minion\StorageChest;
use skyblock\items\sets\types\LeafletArmorSet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;
use skyblock\misc\trades\TradesHandler;

class OakCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::OAK_LOG()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getForagerEggItem(1, MinionHandler::getInstance()->getForagerTypeByBlock(VanillaBlocks::OAK_LOG())),
			2 => TradesHandler::getInstance()->getByOutput(VanillaBlocks::OAK_LEAVES()->asItem()),
			3 => LeafletSet::getInstance()->getPieceItems(),
			4 => (SkyblockItems::STORAGE_CHEST())->setType(StorageChest::TYPE_SMALL),
			5 => ["+500 Foraging XP"],
			6 => SkyblockItems::ENCHANTED_OAK_WOOD(),
			7 => (SkyblockItems::STORAGE_CHEST())->setType(StorageChest::TYPE_MEDIUM),
			8 => SkyblockItems::WOOD_AFFINITY_TALISMAN(),
			9 => (SkyblockItems::STORAGE_CHEST())->setType(StorageChest::TYPE_LARGE),
		];
	}

	public function getName() : string{
		return "Oak Wood";
	}
}