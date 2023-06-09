<?php

declare(strict_types=1);

namespace skyblock\misc\collection\combat;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\ProtectionEnchantment;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\SilkTouch;
use skyblock\items\pets\types\SilverfishPet;
use skyblock\items\pets\types\SpiderPet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedString;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\GraplingHook;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class SpiderCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::STRING();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	//TODO: implement quiver (it's arrow storage)
	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::SPIDER))),
			2 => VanillaBlocks::COBWEB()->asItem(),
			3 => ["Quiver Upgrade"],
			4 => SkyblockItems::ENCHANTED_STRING(),
			5 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::SILK_TOUCH(), 1)),
			6 => ["Large Quiver Upgrade"],
			7 => SkyblockItems::GRAPLING_HOOK(),
			8 => (SkyblockItems::MYSTERY_PET_ITEM())->setPet(new SpiderPet()),
			9 => ["Giant Quiver Upgrade"],
		];
	}

	public function getName() : string{
		return "Spider";
	}
}