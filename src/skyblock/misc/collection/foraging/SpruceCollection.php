<?php

declare(strict_types=1);

namespace skyblock\misc\collection\foraging;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\pets\types\SilverfishPet;
use skyblock\items\pets\types\WolfPet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedSpruceWood;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;
use skyblock\misc\trades\TradesHandler;

class SpruceCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::SPRUCE_LOG()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getForagerEggItem(1, MinionHandler::getInstance()->getForagerTypeByBlock(VanillaBlocks::SPRUCE_LOG())),
			2 => TradesHandler::getInstance()->getByOutput(VanillaBlocks::SPRUCE_LEAVES()->asItem()),
			3 => ["+150 Foraging XP"],
			4 => ["+275 Foraging XP"],
			5 => SkyblockItems::ENCHANTED_SPRUCE_WOOD(),
			6 => ["+575 Foraging XP"],
			7 => ["+1,000 Foraging XP"],
			8 => (SkyblockItems::MYSTERY_PET_ITEM())->setPet(new WolfPet()),
			9 => ["+5,000 Foraging XP"],
		];
	}

	public function getName() : string{
		return "Spruce Wood";
	}
}