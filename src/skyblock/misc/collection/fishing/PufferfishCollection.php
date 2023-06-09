<?php

declare(strict_types=1);

namespace skyblock\misc\collection\fishing;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Cleave;
use skyblock\items\masks\types\PufferfishMask;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\crafting\EnchantedPufferfish;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\items\special\types\fishing\SpikedBait;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\misc\collection\Collection;
use skyskyblock\items\accessory\types\SpeedArtifactAccessory;
use skyskyblock\items\accessory\types\SpeedRingAccessory;

class PufferfishCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::PUFFERFISH();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{

		return [
			1 => PufferfishMask::getItem(),
			2 => SkyblockItems::ENCHANTED_PUFFERFISH(),
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::CLEAVE(), 4)),
			4 => SpikedBait::getItem(),
			5 => (SkyblockItems::FISHING_SACK())->setCapacity(StorageSack::SIZE_SMALL),
			6 => ["+200 Fishing XP"],
			7 => (SkyblockItems::FISHING_SACK())->setCapacity(StorageSack::SIZE_MEDIUM),
			8 => ["Coming Soon"],
			9 => (SkyblockItems::FISHING_SACK())->setCapacity(StorageSack::SIZE_LARGE)
		];
	}

	public function getName() : string{
		return "Pufferfish";
	}
}