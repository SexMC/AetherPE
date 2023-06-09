<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseToggleableEnchant;
use skyblock\items\rarity\Rarity;

class Respiration extends BaseToggleableEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Extends your underwater breathing time by 15*(ce level)");
		$this->setApplicableTo(self::ITEM_HELMET);
		$this->setMaxLevel(2);

		return new CustomEnchantIdentifier("respiration", "Respiration");
	}

	function toggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{

		$player->setMaxAirSupplyTicks($player->getMaxAirSupplyTicks() + (30 * 60));
	}

	function unToggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{

		$player->setMaxAirSupplyTicks($player->getMaxAirSupplyTicks() - (30 * 60));
	}
}