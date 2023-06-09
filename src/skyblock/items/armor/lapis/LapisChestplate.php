<?php

declare(strict_types=1);

namespace skyblock\items\armor\lapis;

use customiesdevs\customies\item\component\ArmorComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\block\utils\DyeColor;
use pocketmine\inventory\ArmorInventory;
use pocketmine\item\ItemIdentifier;
use skyblock\items\armor\ArmorSet;
use skyblock\items\SkyblockArmor;
use skyblock\items\SkyBlockArmorInfo;

class LapisChestplate extends SkyblockArmor implements ItemComponents{
	use ItemComponentsTrait;


	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name, new SkyBlockArmorInfo(4, ArmorInventory::SLOT_CHEST));

		$this->initComponent("lapis_lazuli_chestplate", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_CHESTPLATE));
	}

	public function getArmorSet() : ?ArmorSet{
		return LapisSet::getInstance();
	}
}