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
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedLapis;
use skyblock\items\special\types\crafting\EnchantedLapisBlock;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\misc\collection\Collection;

class LapisCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::LAPIS_LAZULI();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{
	}

	public function getUnlockRecipes() : array{
		return [
			1 => MinionEgg::getMiningEggItem(1, MinionHandler::getInstance()->getMinerTypeByBlock(VanillaBlocks::LAPIS_LAZULI_ORE())),
			2 => VanillaItems::EXPERIENCE_BOTTLE(),
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::EXPERIENCE(), 2)),
			4 => SkyblockItems::ENCHANTED_LAPIS_LAZULI(),
			5 => ["+1,000 Mining XP"],
			6 => ["+1,400 Mining Xp"],
			7 => SkyblockItems::ENCHANTED_LAPIS_BLOCK(),
			8 => ["+2,000 Mining Xp"],
			9 => SkyblockItems::EXPERIENCE_ARTIFACT(),
			//10 => TODO: ADD TEXT BOOK ON UPDATE
		];
	}

	public function getName() : string{
		return "Lapis";
	}
}