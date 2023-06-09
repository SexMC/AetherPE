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
use skyblock\items\customenchants\types\Autosmelt;
use skyblock\items\potions\SkyBlockPotions;
use skyblock\items\potions\types\HastePotion;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedCoal;
use skyblock\items\special\types\crafting\EnchantedCoalBlock;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\EnchantedLavaBucket;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\misc\collection\Collection;

class CoalCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::COAL();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::COAL_ORE())),
			2 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::AUTO_SMELT(), 1)),
			3 => SkyBlockPotions::HASTE(),
			4 => SkyblockItems::ENCHANTED_COAL(),
			5 => (SkyblockItems::MINING_SACK())->setCapacity(StorageSack::SIZE_SMALL),
			6 => SkyblockItems::ENCHANTED_COAL_BLOCK(),
			7 => (SkyblockItems::MINING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM),
			8 => SkyblockItems::ENCHANTED_LAVA_BUCKET(),
			9 =>  ["Coming Soon"],
			10 => (SkyblockItems::MINING_SACK())->setCapacity(StorageSack::SIZE_LARGE),
		];
	}

	public function getName() : string{
		return "Coal";
	}
}