<?php

declare(strict_types=1);

namespace skyblock\items\armor\emerald;

use customiesdevs\customies\item\component\ArmorComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\player\Player;
use skyblock\items\armor\ArmorSet;
use skyblock\items\ItemEditor;
use skyblock\items\SkyblockArmor;
use skyblock\items\SkyBlockArmorInfo;
use skyblock\items\SkyblockItemProperties;
use skyblock\Main;

class EmeraldLeggings extends SkyblockArmor implements ItemComponents{
	use ItemComponentsTrait;


	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name, new SkyBlockArmorInfo(4, ArmorInventory::SLOT_LEGS));

		$this->initComponent("emerald_leggings", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_LEGGINGS));


		$this->properties->setType(SkyblockItemProperties::ITEM_TYPE_ARMOR);
		$this->properties->setUnique(true);

		$this->addComponent(new ArmorComponent(4));
	}

	public function getArmorSet() : ?ArmorSet{
		return EmeraldSet::getInstance();
	}
}