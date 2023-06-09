<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use skyblock\menus\AetherMenu;
use skyblock\sessions\Session;

class EnderInvseeMenu extends AetherMenu {

	protected $type = self::NORMAL;

	protected bool $dupeCheck = false;

	public function __construct(private Session $session){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($this->session->getUsername() . "'s ender chest");

		$menu->getInventory()->setContents($this->session->getEnderchestInventory());


		return $menu;
	}

	public function onNormalTransaction(InvMenuTransaction $transaction) : InvMenuTransactionResult{
		$transaction->getOut()->getNamedTag()->removeTag("menuItem");

		return $transaction->continue();
	}

	public function onClose(Player $player, Inventory $inventory) : void{
		parent::onClose($player, $inventory);

		$this->session->saveEnderchest($player, $inventory);
	}
}