<?php

declare(strict_types=1);

namespace skyblock\menus\shop;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\item\VanillaItems;
use skyblock\forms\commands\ShopBuyForm;
use skyblock\menus\AetherMenu;
use skyblock\misc\shop\Shop;
use skyblock\misc\shop\ShopCategory;
use skyblock\utils\Utils;

class ShopMenu extends AetherMenu {
	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("§6§lShop");

		$this->buildStartMenu($menu);

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$out = $transaction->getOut();
		$player = $transaction->getPlayer();

		if($out->getNamedTag()->getString("category", "") !== ""){
			$this->getMenu()->getInventory()->clearAll();

			$this->buildCategory(Shop::getInstance()->getAllCategories()[$out->getNamedTag()->getString("category")]);
		}

		if($out->getCustomName() === "§c§lGo back"){
			$this->buildStartMenu($this->getMenu());
		}

		if($out->getNamedTag()->getString("shopItem", "") !== ""){
			$player->removeCurrentWindow();

			$item = Shop::getInstance()->getAllItemsByHashId()[$out->getNamedTag()->getString("shopItem")];
			Utils::executeLater(function() use($player, $item): void {
				$player->sendForm(new ShopBuyForm($item, $player));
			}, 10);
		}
	}

	public function buildCategory(ShopCategory $category): void {
		$this->getMenu()->getInventory()->clearAll();

		foreach($category->getItems() as $item){
			$this->getMenu()->getInventory()->addItem($item->getViewItem());
		}

		$this->getMenu()->getInventory()->setItem(53, VanillaItems::ARROW()->setCustomName("§c§lGo back"));
	}

	public function buildStartMenu(InvMenu $menu): void {
		$menu->getInventory()->clearAll();

		foreach(Shop::getInstance()->getAllCategories() as $category){
			$item = $category->getCategoryItem();
			$item->getNamedTag()->setString("category", $category->getCategoryName());
			$menu->getInventory()->addItem($item);
		}
	}
}