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

class Growth extends BaseToggleableEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Gives §a+15 " . PveUtils::getHealth() . "§r§7 per level");
		$this->setApplicableTo(self::ITEM_ARMOUR);
		$this->setMaxLevel(4);

		return new CustomEnchantIdentifier("growth", "Growth");
	}

	function toggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		assert($player instanceof AetherPlayer);
		$player->getPveData()->setHealth($player->getPveData()->getHealth() + 15 * $enchantmentInstance->getLevel());
	}

	function unToggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		assert($player instanceof AetherPlayer);
		$player->getPveData()->setHealth($player->getPveData()->getHealth() - 15 * $enchantmentInstance->getLevel());
	}
}