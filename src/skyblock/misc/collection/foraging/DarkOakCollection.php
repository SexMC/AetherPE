<?php

declare(strict_types=1);

namespace skyblock\misc\collection\foraging;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\armor\growth\GrowthSet;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;
use skyblock\misc\trades\TradesHandler;

class DarkOakCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::DARK_OAK_LOG()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getForagerEggItem(1, MinionHandler::getInstance()->getForagerTypeByBlock(VanillaBlocks::DARK_OAK_LOG())),
			2 => TradesHandler::getInstance()->getByOutput(VanillaBlocks::DARK_OAK_LEAVES()->asItem()),
			3 => ["+150 Foraging XP"],
			4 => ["+275 Foraging XP"],
			5 => SkyblockItems::ENCHANTED_DARK_OAK_WOOD(),
			6 => ["+600 Foraging XP"],
			7 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::GROWTH(), 4)),
			8 => ["+2,500 Foraging XP"],
			9 => GrowthSet::getInstance()->getPieceItems(),
		];
	}

	public function getName() : string{
		return "Dark Oak Wood";
	}
}