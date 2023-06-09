<?php

declare(strict_types=1);

namespace skyblock\menus\commands\auctionhouse;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use skyblock\communication\operations\mechanics\auctionhouse\AuctionHouseRemoveOperation;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\misc\auctionhouse\AuctionHouseHandler;
use skyblock\misc\auctionhouse\AuctionHouseItem;
use skyblock\misc\coinflip\CoinflipHandler;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class AuctionViewMenu extends AetherMenu {
	use AwaitStdTrait;

	public function __construct(private AuctionHouseItem $auction){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_HOPPER);
		$menu->setName("Confirm Purchase");

		$reject = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 14);
		$reject->getNamedTag()->setByte("reject", 1);
		$reject->setCustomName("§cReject");
		$accept = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, 5);
		$accept->setCustomName("§aConfirm");
		$accept->getNamedTag()->setByte("confirm", 1);
		
		$menu->getInventory()->setContents([$accept, $accept, $this->auction->getViewItem(), $reject, $reject]);

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$out = $transaction->getOut();

		if($out->getNamedTag()->getByte("reject", 0) === 1){
			$player->removeCurrentWindow();
			return;
		}

		if($out->getNamedTag()->getByte("confirm", 0) === 1){
			$player->removeCurrentWindow();

			Await::f2c(function() use($player) {
				$session = new Session($player);
				if($this->auction->getPrice() > $session->getPurse()){
					$player->sendMessage(Main::PREFIX . "you don't have enough money to buy this auction");
					return;
				}
				$session->decreasePurse($this->auction->getPrice());

				$success = yield AuctionHouseHandler::getInstance()->removeAuction($this->auction->getAuctionID());
				

				if($success === true){
						(new Session($this->auction->getOwner()))->increasePurse($this->auction->getPrice());
						$item = $this->auction->getItem();
						Utils::addItem($player, $item);
						$player->sendMessage(Main::PREFIX . "§7Successfully bought §c{$item->getName()}§7 for §c$" . number_format($this->auction->getPrice()));
						return;
				}

				$session->increasePurse($this->auction->getPrice());
				$player->sendMessage(Main::PREFIX . "Failed to process the auction.");
			});
			return;
		}
	}
}