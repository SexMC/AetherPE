<?php

declare(strict_types=1);

namespace skyblock\misc\collection\fishing;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Respiration;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedClay;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\items\tools\types\pve\FarmersRod;
use skyblock\misc\collection\Collection;
use skyskyblock\items\accessory\types\SpeedArtifactAccessory;
use skyskyblock\items\accessory\types\SpeedRingAccessory;

class ClayCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::CLAY();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::CLAY())),
			2 => SkyblockItems::ENCHANTED_CLAY(),
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::RESPIRATION(), 2)),
			4 => ["Coming Soon"],
			5 => SkyblockItems::FARMERS_ROD(),
		];
	}

	public function getName() : string{
		return "Clay";
	}
}