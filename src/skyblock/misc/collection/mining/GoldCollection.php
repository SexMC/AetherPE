<?php

declare(strict_types=1);

namespace skyblock\misc\collection\mining;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Scavenger;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedGold;
use skyblock\items\special\types\crafting\EnchantedGoldBlock;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\CleaverSword;
use skyblock\misc\collection\Collection;

class GoldCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::GOLD_INGOT();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::GOLD_ORE())),
			2 => ["+350 Mining XP"],
			3 => ["+650 Mining XP"],
			4 => SkyblockItems::CLEAVER_SWORD(),
			5 => SkyblockItems::ENCHANTED_GOLD(),
			6 => ["+1,000 Mining XP"],
			7 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::SCAVENGER(), 2)),
			8 => SkyblockItems::ENCHANTED_GOLD_BLOCK(),
			9 => ["+3,000 Mining XP"],
		];
	}

	public function getName() : string{
		return "Gold";
	}
}