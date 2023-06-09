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
use SOFe\AwaitGenerator\Await;

class HealingTalismanAccessory extends AccessoryItem implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("healing_talisman", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::GROUP_ARROW));


		$this->getProperties()->setRarity(Rarity::common());

		$this->getProperties()->setDescription([
			"§r§a§l» §r§a+3 " . PveUtils::getHealthSymbol() . " Health Regeneration every 5 seconds",
			"§r§aIf this Talisman is in your §6Accessory Bag§f.",
			"§r",
			"§r§7Regenerate out of thin air.",
		]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "» Healing Talisman «");

		$this->resetLore();
	}

	public function onActivate(AetherPlayer $player, AccessoryItem $item) : void{
		Await::f2c(function() use($player) {
			while($player->isOnline()){
				yield $this->getStd()->sleep(5 * 20);

				if($player->getAccessoryData()->hasAccessory($this)){
					$player->getPveData()->setHealth($player->getPveData()->getHealth() + 3);
				}
			}
		});
	}

	public function onDeactivate(AetherPlayer $player, AccessoryItem $item) : void{}

	public function getAccessoryName() : string{
		return "Healing Talisman";
	}
}