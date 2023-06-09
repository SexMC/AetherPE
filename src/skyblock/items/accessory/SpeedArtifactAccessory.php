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

class SpeedArtifactAccessory extends AccessoryItem implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("speed_artifact", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::GROUP_ARROW));


		$this->getProperties()->setRarity(Rarity::rare());

		$this->getProperties()->setDescription([
			"§r§3§l» §r§3+5 " . PveUtils::getSpeedSymbol() . "§3 Movement Speed",
			"§r§3If this Accessory is in your §6Accessory Bag§a.",
			"§r",
			"§r§7Receive speed out of thin air.",
		]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "» Speed Artifact «");

		$this->resetLore();
	}

	public function onActivate(AetherPlayer $player, AccessoryItem $item) : void{
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() + 5);
	}

	public function onDeactivate(AetherPlayer $player, AccessoryItem $item) : void{
		$player->getPveData()->setSpeed($player->getPveData()->getSpeed() - 5);
	}

	public function getAccessoryName() : string{
		return "Speed Artifact";
	}
}