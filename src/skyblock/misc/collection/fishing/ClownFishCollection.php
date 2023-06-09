<?php

declare(strict_types=1);

namespace skyblock\misc\collection\fishing;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\CustomEnchants;
use skyblock\items\customenchants\types\Magnet;
use skyblock\items\masks\types\ClownfishMask;
use skyblock\items\SkyblockItems;
use skyblock\items\special\types\CustomEnchantmentBookGenerator;
use skyblock\misc\collection\Collection;
use skyblock\misc\trades\TradesHandler;
use skyskyblock\items\accessory\types\SpeedArtifactAccessory;
use skyskyblock\items\accessory\types\SpeedRingAccessory;

class ClownFishCollection extends Collection {


	public function getItem() : Item{
		return VanillaItems::CLOWNFISH();
	}

	public function onLevelUp(Player $player, int $oldLevel, int $newLevel) : void{}

	public function getUnlockRecipes() : array{
		return [
			1 => ClownfishMask::getItem(),
			2 => TradesHandler::getInstance()->getByOutput(VanillaItems::WATER_BUCKET()),
			3 => (SkyblockItems::ENCHANTMENT_BOOK())->addCustomEnchant(new CustomEnchantInstance(CustomEnchants::MAGNET(), 4)),
		];
	}

	public function getName() : string{
		return "Clown Fish";
	}
}