<?php

declare(strict_types=1);

namespace skyblock\items\misc\minion;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class DiamondSpreading extends SkyblockItem implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("diamond_spreading", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));


		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Diamond Spreading");
		$this->getProperties()->setDescription([
			"§r§7This item can be used as a",
			"§r§7minion upgrade. It will ",
			"§r§7occasionally generate diamonds.",
		]);
		$this->resetLore();
		$this->resetLore();
		$this->properties->setUnique(true);

	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::rare());
	}
}