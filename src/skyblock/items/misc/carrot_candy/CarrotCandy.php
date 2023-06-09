<?php

declare(strict_types=1);

namespace skyblock\items\misc\carrot_candy;

use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\player\Player;
use pocketmine\world\sound\XpLevelUpSound;
use skyblock\items\pets\Pet;
use skyblock\items\pets\PetInstance;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\Main;

abstract class CarrotCandy extends SkyblockItem implements ItemComponents{
	use ItemComponentsTrait;


	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->properties->setRarity($this->getRarity());

		$this->properties->setDescription([
			"§r§7Feeding carrot candy to your",
			"§r§7pets gives them a boost of exp",
			"§r§7but pets can only eat §a10",
			"§r§7candy in their lifetime so",
			"§r§7choose your candy wisely!",
			"§r",
			"§r§7Grants §a" . number_format($this->getGain()) . " §7pet exp",
			"§r",
			"§r§eDrag n' Drop this to a pet to",
			"§r§efeed it this candy.",
		]);

		$this->setCustomName("§r" . $this->getRarity()->getColor() . ucwords($this->getCandyName()) . " Carrot Candy");
		$this->resetLore();
	}

	public abstract function getGain(): int;

	public abstract function getRarity(): Rarity;
	public abstract function getCandyName(): string;

	public function onTransaction(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $itemClickedAction, SlotChangeAction $itemClickedWithAction, InventoryTransactionEvent $event) : void{
		if(!$itemClickedWith instanceof CarrotCandy) return;

		$amount = $this->getGain();

		if($amount === 0) return;
		$pet = PetInstance::fromItem($itemClicked);


		if($pet === null){
			$player->sendMessage(Main::PREFIX . "Carrot candy can only be applied to pets");
			return;
		}

		if($pet->getLevel() >= 100){
			$player->sendMessage(Main::PREFIX . "Pet is already max level");
			return;
		}


		if($pet->getCandyUsed() >= 10){
			$player->sendMessage(Main::PREFIX . "You cannot use more than 10 pet candies on a pet");
			return;
		}


		$amount += $pet->getXp();

		for($i = $pet->getLevel() + 1; $i <= 100; $i++){
			$needed = Pet::getXpNeeded($pet->getRarity(), $i);

			if($amount > $needed){
				$pet->setLevel($pet->getLevel()+1);
				$amount -= $needed;

				$player->getWorld()->addSound($player->getPosition(), new XpLevelUpSound(30), [$player]);
			} else {
				$pet->setXp($amount);
				break;
			}
		}

		$player->sendMessage(Main::PREFIX . "Successfully applied §c{$itemClickedWith->getName()} §r§7on " . ($pet->getPet()->getColor($pet->getRarity()) . $pet->getPet()->getName()));
		$pet->setCandyUsed($pet->getCandyUsed() + 1);

		$itemClickedWith->pop();
		$event->cancel();
		$itemClickedWithAction->getInventory()->setItem($itemClickedWithAction->getSlot(), $itemClickedWith);
		$itemClickedAction->getInventory()->setItem($itemClickedAction->getSlot(), $pet->buildPetItem());
	}
}