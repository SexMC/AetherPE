<?php

declare(strict_types=1);

namespace skyblock\misc\collection\farming;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedGlisteringMelon;
use skyblock\items\special\types\crafting\EnchantedMelon;
use skyblock\items\special\types\crafting\EnchantedMelonBlock;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;
use skyskyblock\items\accessory\types\SpeedArtifactAccessory;
use skyskyblock\items\accessory\types\SpeedRingAccessory;

class MelonCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::MELON();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getFarmingEggItem(1, MinionHandler::getInstance()->getFarmerTypeByBlock(VanillaBlocks::MELON())),

			2 => ["+50 Farming Experience"],
			3 => ["+125 Farming Experience"],
			4 => SkyblockItems::ENCHANTED_MELON(),
			5 => SkyblockItems::ENCHANTED_GLISTERING_MELON(),
			6 => SkyblockItems::ENCHANTED_MELON_BLOCK(),
			7 => ["+1,000 Farming Experience"],
			8 => ["+2,000 Farming Experience"],
			9 => ["+3,000 Farming Experience"],
		];
	}

	public function getName() : string{
		return "Melon";
	}
}