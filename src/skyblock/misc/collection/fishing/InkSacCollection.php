<?php

declare(strict_types=1);

namespace skyblock\misc\collection\fishing;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Angler;
use skyblock\items\customenchants\types\Caster;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedInkSac;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\tools\types\pve\InkWand;
use skyblock\misc\collection\Collection;
use skyskyblock\items\accessory\types\SpeedArtifactAccessory;
use skyskyblock\items\accessory\types\SpeedRingAccessory;

class InkSacCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::INK_SAC();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{


		return [
			1 =>["+25 Fishing XP"],
			2 => ["+50 Fishing XP"],
			3 => SkyblockItems::ENCHANTED_INK_SAC(),
			4 => ["+100 Fishing XP"],
			5 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::CASTER(), 4)),
			6 => ["+200 Fishing XP"],
			7 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::ANGLER(), 4)),
			8 => ["+300 Fishing XP"],
			9 => SkyblockItems::INK_WAND(),
		];
	}

	public function getName() : string{
		return "Ink Sac";
	}
}