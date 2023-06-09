<?php

declare(strict_types=1);

namespace skyblock\items\accessory;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\EventPriority;
use pocketmine\item\ItemIdentifier;
use pocketmine\Server;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;
use SOFe\AwaitGenerator\Await;

class VaccineTalismanAccessory extends AccessoryItem implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("vaccine_talisman", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::GROUP_ARROW));


		$this->getProperties()->setRarity(Rarity::common());

		$this->getProperties()->setDescription([
			"§r§f§l» §r§fImmune to §aPoison §fwhen this Talisman",
			"§r§fIf this Accessory is in your §6Accessory Bag§f.",
			"§r",
			"§r§7Why get the vaccine when you can just hold it?",
		]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "» Vaccine Talisman «");

		$this->resetLore();

		Await::f2c(function() {
			while(Server::getInstance()->isRunning()){
				$event = yield $this->getStd()->awaitEvent(EntityDamageEvent::class, fn(EntityDamageEvent $e) => true, false, EventPriority::NORMAL, false);
				assert($event instanceof EntityDamageEvent);
				$p = $event->getEntity();

				if($p instanceof AetherPlayer && $event->getCause() === EntityDamageEvent::CAUSE_MAGIC){
					if($p->getAccessoryData()->hasAccessory($this)){
						$event->cancel();
					}
				}
			}
		});
	}

	public function onActivate(AetherPlayer $player, AccessoryItem $item) : void{}

	public function onDeactivate(AetherPlayer $player, AccessoryItem $item) : void{}

	public function getAccessoryName() : string{
		return "Vaccine Talisman";
	}
}