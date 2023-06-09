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

class ArachneFragment extends SkyblockItem implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("arachne_fragment", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Arachne Fragment");
		$this->getProperties()->setDescription([
			"§r§7A fragment inbued with the",
			"§r§5power of §4Arachne",
		]);

		$this->resetLore();
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::rare());
	}
}