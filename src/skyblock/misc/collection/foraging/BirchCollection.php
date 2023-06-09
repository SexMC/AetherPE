<?php

declare(strict_types=1);

namespace skyblock\misc\collection\foraging;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedBirchWood;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\misc\collection\Collection;
use skyblock\misc\trades\TradesHandler;

class BirchCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::BIRCH_LOG()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getForagerEggItem(1, MinionHandler::getInstance()->getForagerTypeByBlock(VanillaBlocks::BIRCH_LOG())),
			2 => TradesHandler::getInstance()->getByOutput(VanillaBlocks::BIRCH_LEAVES()->asItem()),
			3 => ["+150 Foraging XP"],
			4 => ["+275 Foraging XP"],
			5 => SkyblockItems::ENCHANTED_BIRCH_WOOD(),
			6 => (SkyblockItems::FORAGING_SACK())->setCapacity(StorageSack::SIZE_SMALL),
			7 => (SkyblockItems::FORAGING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM),
			8 => ["+575 Foraging XP"],
			9 => (SkyblockItems::FORAGING_SACK())->setCapacity(StorageSack::SIZE_LARGE),
		];
	}

	public function getName() : string{
		return "Birch Wood";
	}
}