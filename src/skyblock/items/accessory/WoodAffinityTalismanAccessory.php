<?php

declare(strict_types=1);

namespace skyblock\items\accessory;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;

class WoodAffinityTalismanAccessory extends AccessoryItem implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("wood_affinity_talisman", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::GROUP_ARROW));

		$this->getProperties()->setRarity(Rarity::uncommon());
		$this->getProperties()->setDescription([
			"§r§a§l» §r§a+10 " . PveUtils::getHealthSymbol() . "§a Movement Speed",
			"§r§aIf this Accessory is in your §6Accessory Bag§f.",
			"§r",
			"§r§7How come Trees don't grow any fast?",
		]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "» Wood Affinity Talisman «");

		$this->resetLore();
	}

	public function onActivate(AetherPlayer $player, AccessoryItem $item) : void{
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() + 10);
	}

	public function onDeactivate(AetherPlayer $player, AccessoryItem $item) : void{
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() - 10);
	}

	public function getAccessoryName() : string{
		return "Wood Affinity Talisman";
	}
}