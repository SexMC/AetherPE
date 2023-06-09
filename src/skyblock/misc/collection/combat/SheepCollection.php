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

class SheepCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::RAW_MUTTON();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::SHEEP))),
			2 => ["+20 Combat XP"],
			3 => ["+50 Combat XP"],
			4 => SkyBlockPotions::MANA(),
			5 => [SkyblockItems::ENCHANTED_MUTTON(), (SkyblockItems::HUSBANDRY_SACK())->setCapacity(StorageSack::SIZE_SMALL)],
			6 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::RAINBOW(), 1)),
			7 => [(SkyblockItems::HUSBANDRY_SACK())->setCapacity(StorageSack::SIZE_MEDIUM)], //todo: add sheep pet here
			8 => SkyblockItems::ENCHANTED_COOKED_MUTTON(),
			9 => (SkyblockItems::HUSBANDRY_SACK())->setCapacity(StorageSack::SIZE_LARGE),
			//TODO: horns of torment: epic power stone 10 =>
		];
	}

	public function getName() : string{
		return "Muton";
	}
}