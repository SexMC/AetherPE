<?php

declare(strict_types=1);

namespace skyblock\items\misc;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class SuperEchantedEgg extends SkyblockItem implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("super_enchanted_egg", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Super Enchanted Egg");
		$this->getProperties()->setDescription([
			"§r§7Used to craft epic and",
			"§r§7legends pets.",
		]);

		$this->resetLore();
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::rare());
	}
}