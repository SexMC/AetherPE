<?php

declare(strict_types=1);

namespace skyblock\misc\collection\mining;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\FirstStrike;
use skyblock\items\customenchants\types\Sharpness;
use skyblock\items\potions\SkyBlockPotions;
use skyblock\items\potions\types\CriticalPotion;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedFlint;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\FlintShovel;
use skyblock\misc\collection\Collection;

class GravelCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::GRAVEL()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::GRAVEL())),
			2 => SkyblockItems::FLINT_SHOVEL(),
			3 => ["+650 Mining XP"],
			4 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::SHARPNESS(), 4)),
			5 => SkyblockItems::ENCHANTED_FLINT(),
			6 => ["+1,000 Mining XP"],
			7 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::FIRST_STRIKE(), 3)),
			8 => SkyBlockPotions::CRITICAL(),
			9 => ["+5,000 Mining XP"],
		];
	}

	public function getName() : string{
		return "Gravel";
	}
}