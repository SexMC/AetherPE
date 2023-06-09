<?php

declare(strict_types=1);

namespace skyblock\items\misc\carrot_candy;

use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class UltimateCarrotCandyUpgrade extends SkyblockItem {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Ultimate Carrot Candy Upgrade");
		$this->getProperties()->setDescription([
			"§r§7Craft with §5Superb Carrot",
			"§r§5Candies §7efficiently to have them",
			"§r§7grant §a1,000,000 §7pet exp",
		]);

		$this->resetLore();
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::special());
	}
}