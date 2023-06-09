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

class Critical extends BaseToggleableEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Increases " . PveUtils::getCritDamage() . "§r by 20*level");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(1);

		return new CustomEnchantIdentifier("critical", "Critical");
	}

	function toggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setCritDamage($player->getPveData()->getCritDamage() + 20 * $enchantmentInstance->getLevel());
	}

	function unToggle(Player $player, Item $item, CustomEnchantInstance $enchantmentInstance) : void{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setCritDamage($player->getPveData()->getCritDamage() - 20 * $enchantmentInstance->getLevel());
	}
}