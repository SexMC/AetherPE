<?php

declare(strict_types=1);

namespace skyblock\items\armor\farm;

use customiesdevs\customies\item\component\ArmorComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\ItemIdentifier;
use skyblock\items\armor\ArmorSet;
use skyblock\items\SkyblockArmor;
use skyblock\items\SkyBlockArmorInfo;

class FarmHelmet extends SkyblockArmor implements ItemComponents{
	use ItemComponentsTrait;


	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name, new SkyBlockArmorInfo(4, ArmorInventory::SLOT_HEAD));

		$this->initComponent("farm_helmet", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_HELMET));

		$this->addComponent(new ArmorComponent(4));
	}

	public function getArmorSet() : ?ArmorSet{
		return FarmSet::getInstance();
	}
}