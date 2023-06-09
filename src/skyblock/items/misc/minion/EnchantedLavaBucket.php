<?php

declare(strict_types=1);

namespace skyblock\items\misc\minion;


use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class EnchantedLavaBucket extends SkyblockItem implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("custom_lava_bucket", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));


		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Enchanted Lava Bucket");
		$this->getProperties()->setDescription([
			"§r§7Increases the speed of your",
			"§r§7minion by §a25%§7. Unlimited",
			"§r§7duration!",
		]);

		$this->makeGlow();
		$this->resetLore();
		$this->properties->setUnique(true);
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::epic());
	}
}