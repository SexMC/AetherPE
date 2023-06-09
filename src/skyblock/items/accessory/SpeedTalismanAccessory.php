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

class SpeedTalismanAccessory extends AccessoryItem implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("speed_talisman", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::GROUP_ARROW));

		$this->getProperties()->setRarity(Rarity::common());
		$this->getProperties()->setDescription([
			"§r§f§l» §r§a+1 " . PveUtils::getHealthSymbol() . "§f Movement Speed",
			"§r§fIf this Accessory is in your §6Accessory Bag§f.",
			"§r",
			"§r§7Receive Speed.",
		]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "» Speed Ring «");

		$this->resetLore();
	}

	public function onActivate(AetherPlayer $player, AccessoryItem $item) : void{
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() + 1);
	}

	public function onDeactivate(AetherPlayer $player, AccessoryItem $item) : void{
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() - 1);
	}

	public function getAccessoryName() : string{
		return "Speed Talisman";
	}
}