<?php

declare(strict_types=1);

namespace skyblock\misc\collection\farming;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\pumpkin\PumpkinSet;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\masks\types\FarmerMask;
use skyblock\items\masks\types\LanternMask;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;

class PumpkinCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::PUMPKIN()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getFarmingEggItem(1, MinionHandler::getInstance()->getFarmerTypeByBlock(VanillaBlocks::PUMPKIN())),

			2 => PumpkinSet::getInstance()->getPieceItems(),
			3 => [SkyblockItems::ENCHANTED_PUMPKIN()],
			4 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::CUBISM(), 4)),
			5 => ["+500 Farming Experience"],
			6 => ["+750 Farming Experience"],
			7 => LanternMask::getItem(),
			8 => SkyblockItems::POLISHED_PUMPKIN(),
			//9 => FarmerMask::getItem(),
		];
	}

	public function getName() : string{
		return "Pumpkin";
	}
}