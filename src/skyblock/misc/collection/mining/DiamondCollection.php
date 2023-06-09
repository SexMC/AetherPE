<?php

declare(strict_types=1);

namespace skyblock\misc\collection\mining;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\hardened_diamond\HardenedDiamondSet;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Critical;
use skyblock\items\customenchants\types\Execute;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;

class DiamondCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::DIAMOND();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::DIAMOND_ORE())),
			2 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::EXECUTE(), 4)),
			3 => ["+500 Mining XP"],
			4 => SkyblockItems::ENCHANTED_DIAMOND(),
			5 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::CRITICAL(), 4)),
			6 => SkyblockItems::DIAMOND_SPREADING(),
			7 => HardenedDiamondSet::getInstance()->getPieceItems(),
			8 => SkyblockItems::ENCHANTED_DIAMOND_BLOCK(),
		];
	}

	public function getName() : string{
		return "Diamond";
	}
}