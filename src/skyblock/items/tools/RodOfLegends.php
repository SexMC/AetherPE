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

class RodOfLegends extends SkyBlockFishingRod {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		//TODO: requires fishing skill 20

		parent::__construct($identifier, $name);

		$this->properties->setDescription([
			"§r§7A lot of legends, a lot of people,",
			"§r§7have come before me. But this is",
			"§r§7my time."
		]);

		$this->properties->setRarity(Rarity::epic());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 130));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 120));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::SEA_CREATURE_CHANCE(), 6));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::FISHING_SPEED(), 105));
	}
}