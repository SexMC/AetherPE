<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;

class BragMenu extends AetherMenu {

	public function __construct(private string $menuName, private array $inventoryContents, private array $armorContents){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($this->menuName);

		$menu->getInventory()->setContents($this->getDefaultInventory());

		$menu->setListener(function(InvMenuTransaction $transaction){
			return $transaction->discard();
		});

		return $menu;
	}

	public function getDefaultInventory(): array {
		$contents = $this->inventoryContents;
		$contents[47] = $this->armorContents[0] ?? VanillaBlocks::AIR()->asItem();
		$contents[48] = $this->armorContents[1] ?? VanillaBlocks::AIR()->asItem();
		$contents[50] = $this->armorContents[2] ?? VanillaBlocks::AIR()->asItem();
		$contents[51] = $this->armorContents[3] ?? VanillaBlocks::AIR()->asItem();

		$contents[46] = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" ");
		$contents[45] = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" ");
		$contents[49] = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" ");
		$contents[52] = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" ");
		$contents[53] = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 10)->setCustomName(" ");

		return $contents;
	}
}