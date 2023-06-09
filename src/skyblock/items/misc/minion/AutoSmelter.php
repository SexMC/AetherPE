<?php

declare(strict_types=1);

namespace skyblock\items\misc\minion;

use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class AutoSmelter extends SkyblockItem {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Auto Smelter");
		$this->getProperties()->setDescription([
			"§r§7This item can be used as a",
			"§r§7minion upgrade. This will",
			"§r§7automatically smelt materials",
			"§r§7that a minion produces",
		]);

		$this->properties->setUnique(true);
		$this->resetLore();
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::common());
	}
}