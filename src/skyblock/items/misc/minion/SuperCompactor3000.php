<?php

declare(strict_types=1);

namespace skyblock\items\misc\minion;


use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class SuperCompactor3000 extends SkyblockItem {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);


		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Super Compactor 3000");
		$this->getProperties()->setDescription([
			"§r§7This item can be used as a",
			"§r§7minion upgrade. This will",
			"§r§7automatically turn materials",
			"§r§7that a minion produces into",
			"§r§7their enchanted form when there",
			"§r§7are enough resources in the",
			"§r§7minion's storage",
		]);
		$this->resetLore();

		$this->properties->setUnique(true);


	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::rare());
	}
}