<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\utils\Utils;

class TradeMenu extends AetherMenu {

	protected $type = self::NORMAL;

	protected array $playerOneSlots = [];

	protected array $playerTwoSlots = [];

	protected array $acceptSlots = [0, 27];

	private bool $done = false;
	private bool $gave = false;
	private bool $closing = false;

	private bool $closed1 = false;
	private bool $closed2 = false;

	public function __construct(private Player $player1, private Player $player2){
		$this->playerOneSlots = range(1, 26);
		$this->playerTwoSlots = range(28, 53);

		parent::__construct();
	}

	public function close(): void {
		if($this->closed1 === false){
			$this->player1->removeCurrentWindow();
		}

		if($this->closed2 === false){
			$this->player2->removeCurrentWindow();
		}
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($this->player1->getName() . " trading " . $this->player2->getName());

		$menu->getInventory()->setItem($this->acceptSlots[0], $this->getAcceptItem($this->player1));
		$menu->getInventory()->setItem($this->acceptSlots[1], $this->getAcceptItem($this->player2));

		return $menu;
	}

	public function onClose(Player $player, Inventory $inventory) : void{
		if($player->getName() === $this->player1->getName()){
			$this->closed1 = true;
		} else $this->closed2 = true;

		$this->close();

		$this->closing = true;
		parent::onClose($player, $inventory);

		if($this->done === false){
			if($this->gave === false){


				$this->gave = true;

				foreach($this->playerOneSlots as $slot){
					if(!(($i = $this->getMenu()->getInventory()->getItem($slot))->isNull())){
						Utils::addItem($this->player1, $i);
					}
				}

				foreach($this->playerTwoSlots as $slot){
					if(!(($i = $this->getMenu()->getInventory()->getItem($slot))->isNull())){
						Utils::addItem($this->player2, $i);
					}
				}
			}

			$player->sendMessage(Main::PREFIX . "Trade has been cancelled.");
		}
	}

	public function onNormalTransaction(InvMenuTransaction $transaction) : InvMenuTransactionResult{
		if($this->closing === true){
			return $transaction->discard();
		}

		$player = $transaction->getPlayer();
		$out = $transaction->getOut();
		$slot = $transaction->getAction()->getSlot();

		if($out->getNamedTag()->getString("accept", "") !== ""){
			$p = $out->getNamedTag()->getString("accept", "");

			if($p === $player->getName() && $slot === $this->acceptSlots[0]){
				$this->menu->getInventory()->setItem($this->acceptSlots[0], $this->getRejectItem($player));
				$this->done = $this->checkTrade();
			}

			if($p === $player->getName() && $slot === $this->acceptSlots[1]){
				$this->menu->getInventory()->setItem($this->acceptSlots[1], $this->getRejectItem($player));
				$this->done = $this->checkTrade();
			}
		}

		if($out->getNamedTag()->getString("reject", "") !== ""){
			$this->menu->getInventory()->setItem($slot, $this->getAcceptItem($player));

			return $transaction->discard();
		}


		if($player->getName() === $this->player1->getName()){
			if(in_array($slot, $this->playerOneSlots)){

				$this->menu->getInventory()->setItem($this->acceptSlots[0], $this->getAcceptItem($this->player1));
				$this->menu->getInventory()->setItem($this->acceptSlots[1], $this->getAcceptItem($this->player2));
				return $transaction->continue();
			}
		}

		if($player->getName() === $this->player2->getName()){
			if(in_array($slot, $this->playerTwoSlots)){

				$this->menu->getInventory()->setItem($this->acceptSlots[0], $this->getAcceptItem($this->player1));
				$this->menu->getInventory()->setItem($this->acceptSlots[1], $this->getAcceptItem($this->player2));
				return $transaction->continue();
			}
		}


		return $transaction->discard();
	}

	public function checkTrade(): bool {
		$all = true;

		if(!$this->player1->isOnline() || !$this->player2->isOnline()){
			return false;
		}

		foreach($this->acceptSlots as $slot){
			if($this->getMenu()->getInventory()->getItem($slot)->getMeta() === 14){
				$all = false;
				break;
			}
		}

		if($all === true){
			$this->done = true;
			$this->close();

			foreach($this->playerOneSlots as $slot) {
				if(!(($i = $this->getMenu()->getInventory()->getItem($slot))->isNull())){
					Utils::addItem($this->player2, $i);
				}
			}

			foreach($this->playerTwoSlots as $slot) {
				if(!(($i = $this->getMenu()->getInventory()->getItem($slot))->isNull())){
					Utils::addItem($this->player1, $i);
				}
			}

			$this->player1->sendMessage(Main::PREFIX . "Trade has been successfully done");
			$this->player2->sendMessage(Main::PREFIX . "Trade has been successfully done");

			return true;
		}

		return false;
	}

	public function getAcceptItem(Player $player): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 14);
		$item->setCustomName("§aClick to accept");
		$item->getNamedTag()->setString("accept", $player->getName());

		return $item;
	}

	public function getRejectItem(Player $player): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 5);
		$item->setCustomName("§aTrade accepting...");
		$item->setLore(["§7Click me to cancel the trade"]);
		$item->getNamedTag()->setString("reject", $player->getName());

		return $item;
	}

	public function getBarItem(): Item {
		$item = VanillaBlocks::IRON_BARS()->asItem();
		$item->setCustomName(" ");
		$item->getNamedTag()->setByte("ironbar", 1);

		return $item;
	}
}