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
use skyblock\items\customenchants\types\Efficiency;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedRedstone;
use skyblock\items\special\types\crafting\EnchantedRedstoneBlock;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\special\types\PersonalCompactorItem;
use skyblock\misc\collection\Collection;
use skyblock\player\AetherPlayer;

class RedstoneCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::REDSTONE_DUST();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
		if($newLevel === 2 || $newLevel === 5 || $newLevel === 6 || $newLevel === 8){
			if($player instanceof AetherPlayer){
				$player->getAccessoryData()->setExtraAccessorySlots($player->getAccessoryData()->getExtraAccessorySlots() + 1);
			}
		}
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::REDSTONE_ORE())),
			2 => ["+1 Accessory Bag Slot"],
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::EFFICIENCY(), 4)),
			4 => SkyblockItems::ENCHANTED_REDSTONE(),
			5 => ["+1 Accessory Bag Slot"],
			6 => ["+1 Accessory Bag Slot"],
			7 => SkyblockItems::ENCHANTED_REDSTONE_BLOCK(),
			8 => ["+1 Accessory Bag Slot"],
			9 => SkyblockItems::PERSONAL_COMPACTOR_4000(),
		];
	}

	public function getName() : string{
		return "Redstone";
	}
}