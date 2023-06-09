<?php

declare(strict_types=1);

namespace skyblock\misc\collection\farming;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\speedster\SpeedsterSet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;

class SugarcaneCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::SUGARCANE()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getFarmingEggItem(1, MinionHandler::getInstance()->getFarmerTypeByBlock(VanillaBlocks::SUGARCANE())),

			2 => SkyblockItems::SPEED_TALISMAN(),
			3 => ["+50 Farming Experience"],
			4 => SkyblockItems::ENCHANTED_SUGAR(),
			5 => [SkyblockItems::ENCHANTED_PAPER()],
			6 => SkyblockItems::SPEED_RING(),
			7 => SkyblockItems::ENCHANTED_SUGARCANE(),
			8 => SkyblockItems::SPEED_ARTIFACT(),
			9 => SpeedsterSet::getInstance()->getPieceItems(),
		];
	}

	public function getName() : string{
		return "Sugarcane";
	}
}