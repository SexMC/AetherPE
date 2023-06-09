<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use pocketmine\inventory\Inventory;
use pocketmine\player\Player;
use skyblock\menus\AetherMenu;
use skyblock\sessions\Session;

class ArmorInvseeMenu extends AetherMenu {

	protected $type = self::NORMAL;

	protected bool $dupeCheck = false;

	public function __construct(private Session $session){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_HOPPER);
		$menu->setName($this->session->getUsername() . "'s armor inventory");

		$menu->getInventory()->setContents($this->session->getArmorInventory());


		return $menu;
	}

	public function onClose(Player $player, Inventory $inventory) : void{
		parent::onClose($player, $inventory);

		$this->session->saveArmorInventory($player, $inventory);
	}
}