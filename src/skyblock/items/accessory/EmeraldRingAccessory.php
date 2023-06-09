<?php

declare(strict_types=1);

namespace skyblock\items\accessory;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\event\EventPriority;
use pocketmine\item\ItemIdentifier;
use pocketmine\Server;
use skyblock\events\economy\PlayerExperienceGainEvent;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;
use SOFe\AwaitGenerator\Await;

class EmeraldRingAccessory extends AccessoryItem implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("emerald_ring", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::GROUP_ARROW));


		$this->getProperties()->setRarity(Rarity::uncommon());

		$this->getProperties()->setDescription([
			"§r§aGet §6+1 coin§a every minute",
			"§r§ayour §6Accessory Bag§7.",
		]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "» Experience Artifact «");

		$this->resetLore();

	}

	public function onActivate(AetherPlayer $player, AccessoryItem $item) : void{
		Await::f2c(function() use($player) {
			while($player->isOnline()){
				yield $this->getStd()->sleep(20 * 60);

				if($player->getAccessoryData()->hasAccessory($this)){
					$player->getCurrentProfilePlayerSession()->increasePurse(1);
				}
			}
		});

	}

	public function onDeactivate(AetherPlayer $player, AccessoryItem $item) : void{}

	public function getAccessoryName() : string{
		return "Emerald Ring";
	}
}