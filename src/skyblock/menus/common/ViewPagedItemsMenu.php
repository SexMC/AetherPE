<?php

declare(strict_types=1);

namespace skyblock\menus\common;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;

class ViewPagedItemsMenu extends AetherMenu {

	protected array $entries = [];

	protected int $currentPage = 0;

	protected int $size = 52;

	public function __construct(private string $menuName, protected array $contents){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($this->menuName);

		$this->menu = $menu;

		$this->entries = array_chunk($this->contents, $this->size);

		$this->setCurrentPageItems();

		return $menu;
	}

	public function setCurrentPageItems(): void {
		if(!isset($this->entries[$this->currentPage])) return;
		$this->getMenu()->getInventory()->clearAll();

		$current = "§c" . ($this->currentPage + 1) . "§7/§c" . sizeof($this->entries);

		$this->getMenu()->getInventory()->setItem(52, VanillaItems::ARROW()->setCustomName("§7<- Back ($current)"));
		$this->getMenu()->getInventory()->setItem(53, VanillaItems::ARROW()->setCustomName("§7Next Page ($current) ->"));

		$entries = $this->entries[$this->currentPage];

		foreach($entries as $item){
			$this->getMenu()->getInventory()->addItem($item);
		}
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction): void
	{
		if($transaction->getAction()->getSlot() === 52){
			if(isset($this->entries[$this->currentPage - 1])){
				$this->currentPage--;
				$this->setCurrentPageItems();
			}
		} elseif($transaction->getAction()->getSlot() === 53){
			if(isset($this->entries[$this->currentPage + 1])){
				$this->currentPage++;
				$this->setCurrentPageItems();
			}
		}
	}


}