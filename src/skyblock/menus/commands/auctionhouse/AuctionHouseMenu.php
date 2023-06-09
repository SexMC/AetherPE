<?php

declare(strict_types=1);

namespace skyblock\menus\commands\auctionhouse;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\menus\AetherMenu;
use skyblock\misc\auctionhouse\AuctionHouseItem;
use pocketmine\utils\TextFormat as C;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class AuctionHouseMenu extends AetherMenu {

	/** @var AuctionHouseItem[] */
	private array $allAuctions;
	/** @var AuctionHouseItem[] */
	private array $pagedAuctions;

	private int $currentPage = 0;

	public function __construct(private Player $player, array $auctions){
		$auctions = array_reverse($auctions, true);

		$this->allAuctions = $auctions;
		$this->pagedAuctions = array_chunk($auctions, 45);
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Auction House");

		$this->menu = $menu;
		$this->setPage(0);

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$out = $transaction->getOut();

		if($out->getNamedTag()->getByte("previous", 0) === 1){
			$this->setPage(--$this->currentPage);
		}

		if($out->getNamedTag()->getByte("next", 0) === 1){
			$this->setPage(++$this->currentPage);
		}

		if($out->getNamedTag()->getByte("viewSelling", 0) === 1){
			$player->removeCurrentWindow();

			Utils::executeLater(function() use($player){
				(new AuctionViewSellingMenu($this->getPlayerAuctions()))->send($player);
			}, 20);

			return;
		}

		if(($id = $out->getNamedTag()->getString("auctionID", "")) !== ""){
			$player->removeCurrentWindow();

			Utils::executeLater(function() use ($player, $id){
				(new AuctionViewMenu($this->allAuctions[$id]))->send($player);
			}, 20);
		}
	}

	public function setPage(int $page): void {
		$this->getMenu()->getInventory()->clearAll();

		/** @var AuctionHouseItem $v */
		foreach($this->pagedAuctions[$this->currentPage] ?? [] as $v){
			$this->getMenu()->getInventory()->addItem($v->getViewItem());
		}

		$this->getMenu()->getInventory()->setItem(45, $this->getInfoItem());
		$this->getMenu()->getInventory()->setItem(49, $this->getCurrentPageItem($this->currentPage));
		$this->getMenu()->getInventory()->setItem(52, $this->getPotatoItem());
		$this->getMenu()->getInventory()->setItem(53, $this->getChestItem());

		if(isset($this->pagedAuctions[$this->currentPage + 1])){
			$this->getMenu()->getInventory()->setItem(50, $this->getNextPageItem());
		}

		if(isset($this->pagedAuctions[$this->currentPage - 1])){
			$this->getMenu()->getInventory()->setItem(48, $this->getPreviousPageItem());
		}
	}

	public function getNextPageItem(): Item {
		$page = VanillaItems::PAPER();
		$page->setCustomName(C::RED . C::BOLD . "Next page ->");
		$page->setLore([C::GRAY . "View the next page of auctions"]);
		$page->getNamedTag()->setByte("next", 1);

		return $page;
	}

	public function getPreviousPageItem(): Item {
		$page = VanillaItems::PAPER();
		$page->setCustomName(C::GOLD . "<- Previous page");
		$page->setLore([C::GREEN . "View the previous page of auctions"]);
		$page->getNamedTag()->setByte("previous", 1);

		return $page;
	}

	public function getPotatoItem(): Item {
		$potato = VanillaItems::POISONOUS_POTATO();
		$potato->setCustomName(C::RED . C::BOLD . "Expired Items");
		$potato->setLore([C::GRAY . "Expired items go to your /collect"]);

		return $potato;
	}

	public function getCurrentPageItem(int $page): Item {
		$page += 1;
		$item = VanillaItems::CLOCK();
		$item->setCustomName(C::RED . C::BOLD . "Current Page: $page");

		return $item;
	}

	public function getInfoItem(): Item {
		$item = VanillaItems::NETHER_STAR();
		$item->setCustomName(C::RED . C::BOLD . "Auction Commands");
		$item->setLore([C::RED . "/ah" . C::GRAY . " Opens the auction house", C::RED . "/ah <price>" . C::GRAY . " Auction the item in your hand"]);

		return $item;
	}

	public function getChestItem(): Item {
		$diamond = VanillaBlocks::CHEST()->asItem();
		$diamond->getNamedTag()->setByte("viewSelling", 1);
		$diamond->setCustomName(C::RED . C::BOLD . "Items you are selling");
		$diamond->setLore([C::GRAY . "Click here to view all of the items you",
			C::GRAY . "are currently selling on the auction house",
			"",
			C::GRAY . "Selling: " . C::RED . $this->getPlayerAuctionsCount()
		]);

		return $diamond;
	}

	public function getPlayerAuctionsCount(): int {
		$count = 0;
		foreach($this->allAuctions as $auction){
			if(strtolower($auction->getOwner()) === strtolower($this->player->getName())){
				$count++;
			}
		}

		return $count;
	}

	/**
	 * @return AuctionHouseItem[]
	 */
	public function getPlayerAuctions(): array {
		$array = [];
		foreach($this->allAuctions as $auction){
			if(strtolower($auction->getOwner()) === strtolower($this->player->getName())){
				$array[] = $auction;
			}
		}

		return $array;
	}
}