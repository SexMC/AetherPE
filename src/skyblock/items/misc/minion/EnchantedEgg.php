<?php

declare(strict_types=1);

namespace skyblock\items\misc\minion;

use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class EnchantedEgg extends SkyblockItem {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->properties->setDescription([
			"§r§7This item can be used as a",
			"§r§7minion upgrade for chicken minions.",
			"§r§7Guarantees that each chicken will",
			"§r§7drop an egg after they spawan.",
			"§r",
			"§r§7Can also be used to craft",
			"§r§7low-rarity pets.",
		]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Enchanted Egg");

		$this->resetLore();
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::rare());
	}
}