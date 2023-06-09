<?php

declare(strict_types=1);

namespace skyblock\items\misc\minion;

use customiesdevs\customies\item\component\FoodComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class EnchantedHopper extends SkyblockItem implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("enchanted_hopper", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Enchanted Hopper");
		$this->getProperties()->setDescription([
			"§r§7This item can be placed inside",
			"§r§7any minion. Automatically sells generated",
			"§r§7items when the minion has no space. Items",
			"§r§7are sold for §a50%§r§7 of their selling",
			"§r§7price",
		]);


		$this->properties->setUnique(true);
		$this->resetLore();
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::rare());
	}
}