<?php

declare(strict_types=1);

namespace skyblock\menus\trades;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\items\ItemEditor;
use skyblock\menus\AetherMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\misc\trades\TradesHandler;
use skyblock\player\AetherPlayer;

class TradesViewMenu extends AetherMenu {

	private array $slots = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 18, 27, 36, 45, 17, 26, 35, 44, 53, 46, 47, 48, 49, 50, 51, 52];


	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		foreach($this->slots as $slot){
			$menu->getInventory()->setItem($slot, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
		}

		$session = $this->player->getCurrentProfile()->getProfileSession();
		$ids = $session->getAllUnlockedRecipesIdentifiers();
		foreach(TradesHandler::getInstance()->getAll() as $trade){
			if(in_array($trade->getId(), $ids)){
				$menu->getInventory()->addItem($trade->getViewItem());
			} else $menu->getInventory()->addItem($this->getNotUnlockedItem());
		}

		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo back")->setLore(["§r§7To SkyBlock Menu"]));
		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));

		return $menu;
	}
	
	public function getNotUnlockedItem(): Item {
		$item = VanillaItems::GRAY_DYE();
		$item->setCustomName("§r§c???");
		$item->setLore([
			"§r§7Progress through your item",
			"§r§7collections and explore the",
			"§r§7world to unlock new trades",
		]);
		ItemEditor::addUniqueID($item);

		return $item;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$id = $transaction->getItemClicked()->getNamedTag()->getString("id", "");
		$player = $transaction->getPlayer();

		assert($player instanceof AetherPlayer);


		if($slot === 48){
			(new SkyblockMenu($player, $this))->send($player);
			return;
		}

		if($slot === 49){
			$transaction->getPlayer()->removeCurrentWindow();
			return;
		}

		if($id !== ""){
			(new TradeMenu(TradesHandler::getInstance()->getById($id), $this))->send($player);
		}
	}
}