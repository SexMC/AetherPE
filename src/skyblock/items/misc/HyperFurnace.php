<?php

declare(strict_types=1);

namespace skyblock\items\misc;


use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class HyperFurnace extends SkyblockItem {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->getProperties()->setDescription(["§r§7Smelts items instantly."]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Hyper Furnace");

		$this->resetLore();
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::uncommon());
	}

	public function getBlock(?int $clickedFace = null) : Block{
		return VanillaBlocks::FURNACE();
	}
}