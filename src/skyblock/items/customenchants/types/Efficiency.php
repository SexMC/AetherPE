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
use skyblock\utils\PveUtils;

class Efficiency extends BaseToggleableEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Grants §a+20 " . PveUtils::getMiningSpeed() . "§r§7 per level.");
		$this->setApplicableTo(self::ITEM_TOOLS);
		$this->setMaxLevel(5);

		return new CustomEnchantIdentifier("efficiency", "Efficiency", true);
	}

	function toggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setMiningSpeed($player->getPveData()->getMiningSpeed() + 20 * $enchantmentInstance->getLevel());
	}

	function unToggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setMiningSpeed($player->getPveData()->getMiningSpeed() - 20 * $enchantmentInstance->getLevel());
	}
}