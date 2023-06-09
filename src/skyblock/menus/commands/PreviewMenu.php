<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\items\lootbox\types\JealousYetLootbox;
use skyblock\items\lootbox\types\store\April2022AetherCrate;
use skyblock\items\lootbox\types\store\May2022AetherCrate;
use skyblock\items\lootbox\types\store\NeptuneResetOneAetherCrate;
use skyblock\items\lootbox\types\store\PlanetNeptuneLootbox;
use skyblock\menus\AetherMenu;

class PreviewMenu extends AetherMenu {

	public function constructMenu(): InvMenu
	{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		foreach ($menu->getInventory()->getContents(true) as $k => $v){
			$menu->getInventory()->setItem($k, ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, mt_rand(0, 6)));
		}

		$menu->getInventory()->setItem(38, May2022AetherCrate::getItem());

		return $menu;
	}
}