<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseToggleableEnchant;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;

class Harvesting extends BaseToggleableEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("§r§7Grants you §a+12.5 §6farming §6fortune §7per level, which increases your chance for multiple crops");
		$this->setApplicableTo(self::ITEM_HOE);
		$this->setMaxLevel(5);

		return new CustomEnchantIdentifier("harvesting", "Harvesting");
	}

	function toggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		if($player instanceof AetherPlayer){
			$player->getPveData()->setFarmingFortune($player->getPveData()->getFarmingFortune() + 12.5 * $enchantmentInstance->getLevel());
		}
	}

	function unToggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		if($player instanceof AetherPlayer){
			$player->getPveData()->setFarmingFortune($player->getPveData()->getFarmingFortune() - 12.5 * $enchantmentInstance->getLevel());
		}
	}
}