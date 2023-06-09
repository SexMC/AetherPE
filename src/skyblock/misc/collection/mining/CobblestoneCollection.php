<?php

declare(strict_types=1);

namespace skyblock\misc\collection\mining;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\miner\MinerSet;
use skyblock\items\pets\types\SilverfishPet;
use skyblock\items\sets\types\MinersOutfitSet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedCobblestone;
use skyblock\items\special\types\HyperFurnace;
use skyblock\items\special\types\minion\AutoSmelter;
use skyblock\items\special\types\minion\Compactor;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\special\types\minion\SuperCompactor3000;
use skyblock\misc\collection\Collection;

class CobblestoneCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::COBBLESTONE()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::COBBLESTONE())),
			2 => ["+250 Mining XP"],
			3 => SkyblockItems::AUTO_SMELTER(),
			4 => SkyblockItems::ENCHANTED_COBBLESTONE(),
			5 => SkyblockItems::COMPACTOR(),
			6 => (SkyblockItems::MYSTERY_PET_ITEM())->setPet(new SilverfishPet()),
			7 => MinerSet::getInstance()->getPieceItems(),
			8 => SkyblockItems::HYPER_FURNACE(),
			9 => ["+1,750 Mining XP"],
			10 => SkyblockItems::SUPER_COMPACTOR_3000(),
		];
	}

	public function getName() : string{
		return "Cobblestone";
	}
}