<?php

declare(strict_types=1);

namespace skyblock\misc\collection\fishing;

use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\accessory\types\HealingRingAccessory;
use skyblock\items\accessory\types\HealingTalismanAccessory;
use skyblock\items\customenchants\types\Caster;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedLilyPad;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\fishing\WhaleBait;
use skyblock\items\tools\types\pve\RodOfChampions;
use skyblock\items\tools\types\pve\RodOfLegends;
use skyblock\misc\collection\Collection;

class LilyPadCollection extends Collection {


	public function getItem() : Item{
		return VanillaBlocks::LILY_PAD()->asItem();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{


		return [
			1 => ["+25 Fishing XP"],
			2 => ["+50 Fishing XP"],
			3 => SkyblockItems::HEALING_TALISMAN(),
			4 => SkyblockItems::ENCHANTED_LILY_PAD(),
			5 => ["Coming Soon"],
			6 => WhaleBait::getItem(),
			7 => SkyblockItems::ROD_OF_CHAMPIONS(),
			8 => SkyblockItems::HEALING_RING(),
			9 => SkyblockItems::ROD_OF_LEGENDS(),
		];
	}

	public function getName() : string{
		return "Lily Pad";
	}
}