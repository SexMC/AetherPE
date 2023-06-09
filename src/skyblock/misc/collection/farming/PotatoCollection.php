<?php

declare(strict_types=1);

namespace skyblock\misc\collection\farming;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

use skyblock\entity\minion\MinionHandler;

use skyblock\items\accessory\types\VaccineTalismanAccessory;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedBakedPotato;

use skyblock\items\special\types\crafting\EnchantedPotato;

use skyblock\items\special\types\HotpotatoBook;
use skyblock\items\special\types\minion\MinionEgg;
use skyskyblock\items\accessory\types\SpeedArtifactAccessory;
use skyskyblock\items\accessory\types\SpeedRingAccessory;
use skyblock\misc\collection\Collection;

class PotatoCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::POTATO();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getFarmingEggItem(1, MinionHandler::getInstance()->getFarmerTypeByBlock(VanillaBlocks::POTATOES())),

			2 => ["+100 Farming Experience"],
			3 => SkyblockItems::VACCINE_TALISMAN(),
			4 => SkyblockItems::ENCHANTED_POTATO(),
			5 => ["+500 Farming Experience"],
			6 => ["+750 Farming Experience"],
			7 => SkyblockItems::ENCHANTED_BAKED_POTATO(),
			8 => SkyblockItems::HOT_POTATO_BOOK(),
			9 => ["+3,750 Farming Experience"],
		];
	}

	public function getName() : string{
		return "Potato";
	}
}