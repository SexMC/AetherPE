<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Shovel;
use skyblock\items\ability\DestroyConnectedBlocksAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockTool;
use skyblock\player\AetherPlayer;

class FarmersRod extends SkyBlockFishingRod {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		//TODO: requires fishing skill 8

		parent::__construct($identifier, $name);

		$this->properties->setDescription([
			"§r§7Chance to fish up farm animals!",
		]);

		$this->properties->setRarity(Rarity::uncommon());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 50));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 20));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::FISHING_SPEED(), 60));
	}
}