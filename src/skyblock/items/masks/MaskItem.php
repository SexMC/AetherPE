<?php

declare(strict_types=1);

namespace skyblock\items\masks;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\SkyblockArmor;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\Main;

class MaskItem extends SkyblockItem implements IMaskHolder{
	use MaskHolderTrait;


	public function onTransaction(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $itemClickedAction, SlotChangeAction $itemClickedWithAction, InventoryTransactionEvent $event) : void{
		$mask = $this->getMask();

		if(!$itemClicked instanceof SkyblockArmor){
			$player->sendMessage(Main::PREFIX . "Masks can only be applied to helmets");
			return;
		}

		if($itemClicked->getArmorSlot() !== ArmorInventory::SLOT_HEAD){
			$player->sendMessage(Main::PREFIX . "Masks can only be applied to helmets");
			return;
		}

		if($itemClicked->getMask() instanceof Mask){
			$player->sendMessage(Main::PREFIX . "This item already has a mask applied");
			return;
		}

		$player->sendMessage(Main::PREFIX . "Successfully applied the §c{$mask::getName()} §7mask");
		$itemClicked->setMask($mask::getName());
		$itemClickedWith->pop();
		$event->cancel();
		$itemClickedWithAction->getInventory()->setItem($itemClickedWithAction->getSlot(), $itemClickedWith);
		$itemClickedAction->getInventory()->setItem($itemClickedAction->getSlot(), $itemClicked);
	}





	public function buildProperties() : SkyblockItemProperties{
		return new SkyblockItemProperties();
	}


	public function resetLore(array $lore = []) : void{
		if($this->getMask()){
			$mask = $this->getMask();


			//$item = $mask::getItem();
			//$this->properties->setRarity($mask->getRarity());
			//$this->properties->setDescription($item->getLore());
			//$this->setCustomName($item->getCustomName());
		}

		parent::resetLore($lore);
	}
}