<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use skyblock\items\lootbox\types\essence\EssenceLootboxT1;
use skyblock\items\lootbox\types\essence\EssenceLootboxT2;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class EssenceShop extends AetherMenu {
	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$menu->setName("§dEssence Shop");


		$t2 = EssenceLootboxT2::getItem();
		$lore = $t2->getLore();
		$lore[] = "§r";
		$lore[] = "§r§l§cPRICE §r§c500,000 Essence";
		$t2->setLore($lore);

		$menu->getInventory()->addItem($t2);

		$t2 = EssenceLootboxT1::getItem();
		$lore = $t2->getLore();
		$lore[] = "§r";
		$lore[] = "§r§l§cPRICE §r§c100,000 Essence";
		$t2->setLore($lore);

		$menu->getInventory()->addItem($t2);


		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$session = new Session($player = $transaction->getPlayer());

		if($slot === 0){
			if($session->getEssence() >= 500000){
				$session->decreaseEssence(500000);
				Utils::addItem($player, EssenceLootboxT2::getItem());
				$player->sendMessage(Main::PREFIX . "Successfully bought tier 2 essence lootbox for §c500,00§7 essence");
			} else $player->sendMessage(Main::PREFIX . "You don't have §c500,000 §7essence");
		}

		if($slot === 1) {
			if($session->getEssence() >= 100000){
				$session->decreaseEssence(100000);
				Utils::addItem($player, EssenceLootboxT1::getItem());
				$player->sendMessage(Main::PREFIX . "Successfully bought tier 1 essence lootbox for §c100,00§7 essence");
			} else $player->sendMessage(Main::PREFIX . "You don't have §c100,000 §7essence");
		}
	}
}