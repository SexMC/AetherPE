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

class Protection extends BaseToggleableEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Gives you §a+4" . PveUtils::getDefense() . "§r§7 per level.");
		$this->setApplicableTo(self::ITEM_ARMOUR);
		$this->setMaxLevel(5);

		return new CustomEnchantIdentifier("protection", "Protection");
	}

	function toggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setDefense($player->getPveData()->getDefense() + $enchantmentInstance->getLevel() * 4);
	}

	function unToggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setDefense($player->getPveData()->getDefense() - $enchantmentInstance->getLevel() * 4);

	}
}