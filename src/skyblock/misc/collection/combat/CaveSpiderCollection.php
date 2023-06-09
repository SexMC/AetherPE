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
use skyblock\items\customenchants\types\BaneOfArthropods;
use skyblock\items\masks\types\SpiderMask;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedFermentedSpiderEye;
use skyblock\items\special\types\crafting\EnchantedSpiderEye;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\SpiderSword;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class CaveSpiderCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::SPIDER_EYE();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::CAVE_SPIDER))),
			2 => SkyblockItems::SPIDER_SWORD(),
			3 => SpiderMask::getItem(),
			4 => SkyblockItems::ENCHANTED_SPIDER_EYE(),
			5 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::BANE_OF_ARTHROPODS(), 4)),
			6 => ["Coming Soon (CE)"],
			7 => SkyblockItems::ENCHANTED_FERMENTED_SPIDER_EYE(),
			8 => ["+25,000 Combat XP"],
			9 => ["Coming Soon (tools)"],
		];
	}

	public function getName() : string{
		return "Cave Spider";
	}
}