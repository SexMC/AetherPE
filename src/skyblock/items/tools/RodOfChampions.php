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

class RodOfChampions extends SkyBlockFishingRod {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		//TODO: requires fishing skill 15

		parent::__construct($identifier, $name);

		$this->properties->setDescription([
			"§r§7Accept the challenges so that",
			"§r§7you can feel the exhilaration",
			"§r§7of victory."
		]);

		$this->properties->setRarity(Rarity::rare());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 90));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 80));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::SEA_CREATURE_CHANCE(), 4));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::FISHING_SPEED(), 90));
	}
}