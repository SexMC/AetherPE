<?php

declare(strict_types=1);

namespace skyblock\misc\collection\farming;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\accessory\types\FarmingTalismanAccessory;
use skyblock\items\armor\farm\FarmSet;
use skyblock\items\armor\farm_suit\FarmSuitSet;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Harvesting;
use skyblock\items\sets\types\FarmArmorSet;
use skyblock\items\sets\types\FarmingSuitSet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedBread;
use skyblock\items\special\types\crafting\EnchantedHaybale;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\misc\collection\Collection;

class WheatCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::WHEAT();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			//wheat minion1 =>
			1 => MinionEgg::getFarmingEggItem(1, MinionHandler::getInstance()->getFarmerTypeByBlock(VanillaBlocks::WHEAT())),
			2 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::HARVESTING(), 5)),
			3 => FarmSuitSet::getInstance()->getPieceItems(),
			4 => SkyblockItems::FARMING_TALISMAN(),
			5 => SkyblockItems::ENCHANTED_BREAD(),
			6 => SkyblockItems::ENCHANTED_HAY_BALE(),
			7 => (SkyblockItems::AGRONOMY_SACK())->setCapacity(StorageSack::SIZE_SMALL),
			8 => (SkyblockItems::AGRONOMY_SACK())->setCapacity(StorageSack::SIZE_MEDIUM),
			9 => FarmSet::getInstance()->getPieceItems(),
			10 => (SkyblockItems::AGRONOMY_SACK())->setCapacity(StorageSack::SIZE_LARGE),
		];
	}

	public function getName() : string{
		return "Wheat";
	}
}