<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\inventory\Inventory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\menus\AetherMenu;
use skyblock\sessions\Session;

class CollectMenu extends AetherMenu {

	protected $type = self::NORMAL;

	private Session $session;

	protected bool $dupeCheck = false;

	public function __construct(Session $session)
	{
		$this->session = $session;
		parent::__construct();
	}

	public function onNormalTransaction(InvMenuTransaction $transaction): InvMenuTransactionResult
	{
		if ($transaction->getItemClickedWith()->getId() !== ItemIds::AIR) {
			return $transaction->discard();
		}

		return $transaction->continue();
	}

	public function onClose(Player $player, Inventory $inventory): void
	{
		parent::onClose($player, $inventory);
		$this->session->setCollectItems($inventory->getContents());
	}

	public function constructMenu(): InvMenu
	{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName(TextFormat::AQUA . TextFormat::AQUA . "Collect");
		$menu->getInventory()->setContents($this->session->getCollectItems());

		return $menu;
	}
}