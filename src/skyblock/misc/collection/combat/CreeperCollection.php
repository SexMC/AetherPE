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
use skyblock\items\customenchants\types\BlastProtection;
use skyblock\items\customenchants\types\Thunderlord;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedSpiderEye;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\ExplosiveBow;
use skyblock\misc\collection\Collection;
use skyblock\utils\EntityUtils;

class CreeperCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::GUNPOWDER();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getCombatEggItem(1, MinionHandler::getInstance()->getSlayerType(EntityUtils::getEntityNameFromID(EntityIds::CREEPER))),
			2 => ["+150 Combat XP"],
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::BLAST_PROTECTION(), 4)),
			4 => SkyblockItems::ENCHANTED_SPIDER_EYE(),
			5 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::THUNDERLORD(), 4)),
			6 => ["+2,000 Combat XP"],
			7 => ["+5,000 Combat XP"],
			8 => ["+9,000 Combat XP"],
			9 => SkyblockItems::EXPLOSIVE_BOW(),
		];
	}

	public function getName() : string{
		return "Gunpowder";
	}
}