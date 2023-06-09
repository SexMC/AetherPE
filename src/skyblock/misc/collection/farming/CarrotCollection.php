<?php

declare(strict_types=1);

namespace skyblock\misc\collection\farming;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\CarrotCandy;
use skyblock\items\special\types\crafting\EnchantedCarrot;
use skyblock\items\special\types\crafting\EnchantedGlisteringMelon;
use skyblock\items\special\types\crafting\EnchantedGoldenCarrot;
use skyblock\items\special\types\crafting\EnchantedMelon;
use skyblock\items\special\types\crafting\EnchantedMelonBlock;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;

class CarrotCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::CARROT();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getFarmingEggItem(1, MinionHandler::getInstance()->getFarmerTypeByBlock(VanillaBlocks::CARROTS())),
			2 => SkyblockItems::SIMPLE_CARROT_CANDY(),
			3 => ["+300 Farming Experience"],
			4 => SkyblockItems::ENCHANTED_CARROT(),
			5 => ["+2,500 Farming Experience"],
			6 => SkyblockItems::GREAT_CARROT_CANDY(),
			7 => SkyblockItems::ENCHANTED_GOLDEN_CARROT(),
			8 => SkyblockItems::SUPERB_CARROT_CANDY(),
			9 => SkyblockItems::ULTIMATE_CARROT_CANDY(),
		];
	}

	public function getName() : string{
		return "Carrot";
	}
}