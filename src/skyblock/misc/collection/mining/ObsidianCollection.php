<?php

declare(strict_types=1);

namespace skyblock\misc\collection\mining;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\entity\minion\MinionHandler;
use skyblock\items\accessory\types\ExperienceArtifact;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Experience;
use skyblock\items\potions\SkyBlockPotion;
use skyblock\items\potions\SkyBlockPotions;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedLapis;
use skyblock\items\special\types\crafting\EnchantedLapisBlock;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;

class ObsidianCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::OBSIDIAN()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::LAPIS_LAZULI_ORE())),
			2 => ["+150 Mining XP"],
			3 => ["+350 Mining XP"],
			4 => SkyblockItems::ENCHANTED_OBSIDIAN(),
			5 => ["Coming Soon"],
			6 => SkyBlockPotions::STUN(),
			7 => ["Coming Soon"],
			8 => SkyblockItems::TREECAPACITOR(),
			//TODO: reforge stone obsidian tablet
		];
	}

	public function getName() : string{
		return "Obsidian";
	}
}