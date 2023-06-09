<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use skyblock\menus\AetherMenu;

class SeeItemMenu extends AetherMenu {

	public function __construct(private string $menuName, private Item $item) {
		parent::__construct();
	}

	/**
	 * @return Item[]
	 */
	public function getDefaultInventory(): array {
		return [
			ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" "),
			ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" "),
			$this->item,
			ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" "),
			ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" "),
		];
	}

	public function constructMenu(): InvMenu {
		$menu = InvMenu::create(InvMenu::TYPE_HOPPER);
		$menu->setName($this->menuName);
		$menu->getInventory()->setContents($this->getDefaultInventory());
		return $menu;
	}
}