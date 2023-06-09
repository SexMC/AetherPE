<?php

declare(strict_types=1);

namespace skyblock\menus\slotbot;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use PHPUnit\Framework\MockObject\Rule\InvokedAtIndex;
use pocketmine\item\Item;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\misc\slotbot\SlotbotHandler;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class CreditShopMenu extends AetherMenu {
	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setName("Credits Shop");

		$menu->getInventory()->setItem(13, $this->getSoldItem());

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();

		if($transaction->getAction()->getSlot() === 13){
			$session = new Session($player);

			if($session->getSlotCredits() >= 100){
				$session->decreaseSlotCredits(100);

				$i = clone SlotbotHandler::getInstance()->current;
				Utils::addItem($player, $i);
			} else $player->sendMessage(Main::PREFIX . "You don't have enough slot bot credits");
		}
	}

	public function getSoldItem(): Item {
		$item = clone SlotbotHandler::getInstance()->current;

		$lore = $item->getLore();
		$lore[] = "§r";
		$lore[] = "§r§eTotal buys: " . SlotbotHandler::getInstance()->totalBuys;
		$lore[] = "§r§eCost: 100 Slot-Bot Credits";
		$item->setLore($lore);

		return $item;
	}
}