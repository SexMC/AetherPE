<?php

declare(strict_types=1);

namespace skyblock\items\transactions\handlers;

use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use skyblock\items\ItemEditor;
use skyblock\items\masks\Mask;
use skyblock\items\masks\MasksHandler;
use skyblock\items\transactions\BaseTransactionHandler;
use skyblock\Main;
use skyblock\utils\CustomEnchantUtils;

class MaskTransactionHandler extends BaseTransactionHandler {
	public function itemMatchesRequired(Item $item) : bool{
		return $item->getNamedTag()->getString("mask", "") !== "" && $item->getId() === ItemIds::NETHER_STAR;
	}

	public function onTransaction(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $itemClickedAction, SlotChangeAction $itemClickedWithAction, InventoryTransactionEvent $event) : void{
		$mask = MasksHandler::getInstance()->getMask($itemClickedWith->getNamedTag()->getString("mask"));

		if(!CustomEnchantUtils::isHelmet($itemClicked)){
			$player->sendMessage(Main::PREFIX . "Masks can only be applied to helmets");
			return;
		}

		if(ItemEditor::getMask($itemClicked) instanceof Mask){
			$player->sendMessage(Main::PREFIX . "This item already has a mask applied");
			return;
		}

		$player->sendMessage(Main::PREFIX . "Successfully applied the §c{$mask::getName()} §7mask");
		ItemEditor::setMask($itemClicked, $mask);
		$itemClickedWith->pop();
		$event->cancel();
		$itemClickedWithAction->getInventory()->setItem($itemClickedWithAction->getSlot(), $itemClickedWith);
		$itemClickedAction->getInventory()->setItem($itemClickedAction->getSlot(), $itemClicked);
	}
}