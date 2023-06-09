<?php

declare(strict_types=1);

namespace skyblock\items\misc;

use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class SoulString extends SkyblockItem {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Soul String");
		$this->getProperties()->setDescription([
			"§r§7Rare string that can be used",
			"§r§7to craft weapons that are",
			"§r§7infused with the power of",
			"§r§7Arachne.",
		]);
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::rare());
	}
}