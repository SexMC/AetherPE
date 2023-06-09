<?php

declare(strict_types=1);

namespace skyblock\misc\collection\foraging;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\pets\types\OcelotPet;
use skyblock\items\pets\types\SilverfishPet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedJungleWood;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\JungleAxe;
use skyblock\misc\collection\Collection;
use skyblock\misc\trades\TradesHandler;

class JungleCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::JUNGLE_LOG()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getForagerEggItem(1, MinionHandler::getInstance()->getForagerTypeByBlock(VanillaBlocks::JUNGLE_LOG())),
			2 => TradesHandler::getInstance()->getByOutput(VanillaBlocks::JUNGLE_LEAVES()->asItem()),
			3 => TradesHandler::getInstance()->getByOutput(VanillaBlocks::VINES()->asItem()),
			4 => ["+200 Foraging XP"],
			5 => SkyblockItems::ENCHANTED_JUNGLE_WOOD(),
			6 => ["+575 Foraging XP"],
			7 => SkyblockItems::JUNGLE_AXE(),
			8 => ["Coming Soon"],
			//TODO: ocelot pet furture update
		];
	}

	public function getName() : string{
		return "Jungle Wood";
	}
}