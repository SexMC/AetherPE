<?php

declare(strict_types=1);

namespace skyblock\misc\collection\mining;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\golem\GolemSet;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Protection;
use skyblock\items\sets\types\GolemArmorSet;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedGoldBlock;
use skyblock\items\special\types\crafting\EnchantedIron;
use skyblock\items\special\types\crafting\EnchantedIronBlock;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\BudgetHopper;
use skyblock\items\special\types\minion\EnchantedHopper;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;

class IronCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::IRON_INGOT();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::IRON_ORE())),
			2 => ["+350 Mining XP"],
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::PROTECTION(), 4)),
			4 => SkyblockItems::ENCHANTED_IRON(),
			5 => SkyblockItems::BUDGET_HOPPER(),
			6 => GolemSet::getInstance()->getPieceItems(),
			7 => SkyblockItems::ENCHANTED_IRON_BLOCK(),
			8 => SkyblockItems::ENCHANTED_GOLD_BLOCK(),
			9 => SkyblockItems::ENCHANTED_HOPPER(),
		];
	}

	public function getName() : string{
		return "Iron";
	}
}