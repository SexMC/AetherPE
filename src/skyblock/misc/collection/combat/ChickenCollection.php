<?php

declare(strict_types=1);

namespace skyblock\misc\collection\combat;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Rainbow;
use skyblock\items\masks\types\ChickenHeadMask;
use skyblock\items\pets\types\ChickenPet;
use skyblock\items\potions\SkyBlockPotions;
use skyblock\items\potions\types\ManaPotion;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedCookedMutton;
use skyblock\items\special\types\crafting\EnchantedMutton;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class ChickenCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::RAW_CHICKEN();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::CHICKEN))),
			2 => ["+20 Combat XP"],
			3 => ChickenHeadMask::getItem(),
			4 => SkyblockItems::ENCHANTED_CHICKEN(),
			5 => SkyblockItems::ENCHANTED_EGG(),
			6 => (SkyblockItems::MYSTERY_PET_ITEM())->setPet(new ChickenPet()),
			7 => ["Coming Soon"],
			8 => ["Coming Soon"],
			9 => SkyblockItems::SUPER_ENCHANTED_EGG(),
			10 => ["Coming Soon"]
		];
	}

	public function getName() : string{
		return "Raw Chicken";
	}
}