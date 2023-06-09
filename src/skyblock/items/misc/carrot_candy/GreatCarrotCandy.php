<?php

declare(strict_types=1);

namespace skyblock\items\misc\carrot_candy;

use customiesdevs\customies\item\CreativeInventoryInfo;
use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItemProperties;

class GreatCarrotCandy extends CarrotCandy {


	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("great_carrot_candy", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));
	}

	public function getGain() : int{
		return 100000;
	}

	public function getRarity() : Rarity{
		return Rarity::rare();
	}

	public function buildProperties() : SkyblockItemProperties{
		return new SkyblockItemProperties();
	}

	public function getCandyName() : string{
		return "Great";
	}
}