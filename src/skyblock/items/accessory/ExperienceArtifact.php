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

class ExperienceArtifact extends AccessoryItem implements ItemComponents {
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("experience_artifact", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_ITEMS, CreativeInventoryInfo::GROUP_ARROW));


		$this->getProperties()->setRarity(Rarity::epic());

		$this->getProperties()->setDescription([
			"§r§5+25% Bonus Experience if this Artifact is in",
			"§r§5your §6Accessory Bag§7.",
		]);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "» Experience Artifact «");

		$this->resetLore();

		Await::f2c(function() {
			while(Server::getInstance()->isRunning()){
				$event = yield $this->getStd()->awaitEvent(PlayerExperienceGainEvent::class, fn(PlayerExperienceGainEvent $e) => true, false, EventPriority::NORMAL, false);
				assert($event instanceof PlayerExperienceGainEvent);
				$p = $event->getPlayer();

				assert($p instanceof AetherPlayer);

				if($p->getAccessoryData()->hasAccessory($this)){
					$event->addBoost(0.25);
				}
			}
		});
	}

	public function onActivate(AetherPlayer $player, AccessoryItem $item) : void{}

	public function onDeactivate(AetherPlayer $player, AccessoryItem $item) : void{}

	public function getAccessoryName() : string{
		return "Experience Artifact";
	}
}