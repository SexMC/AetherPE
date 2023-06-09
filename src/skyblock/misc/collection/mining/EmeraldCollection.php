<?php

declare(strict_types=1);

namespace skyblock\misc\collection\mining;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\emerald\EmeraldSet;
use skyblock\items\customenchants\types\Scavenger;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedEmerald;
use skyblock\items\special\types\crafting\EnchantedGold;
use skyblock\items\special\types\crafting\EnchantedGoldBlock;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;


//TODO: this is not done!
class EmeraldCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::EMERALD();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::EMERALD_ORE())),
			2 => ["+350 Mining XP"],
			3 => ["+650 Mining XP"],
			4 => SkyblockItems::ENCHANTED_EMERALD(),
			5 => SkyblockItems::EMERALD_RING(),
			6 => ["Coming Soon"],
			7 => SkyblockItems::ENCHANTED_EMERALD_BLOCK(),
			8 => SkyblockItems::EMERALD_SWORD(),
			9 => EmeraldSet::getInstance()->getPieceItems(),
		];
	}

	public function getName() : string{
		return "Emerald";
	}
}