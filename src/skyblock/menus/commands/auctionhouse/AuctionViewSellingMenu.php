<?php

declare(strict_types=1);

namespace skyblock\menus\commands\auctionhouse;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
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

class AuctionViewSellingMenu extends AetherMenu {
	use AwaitStdTrait;

	/**
	 * @param AuctionHouseItem[] $auctions
	 */
	public function __construct(private array $auctions){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Items you are selling");

		foreach($this->auctions as $v){
			$menu->getInventory()->addItem($v->getViewItem());
		}

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction): void{
		$player = $transaction->getPlayer();
		$out = $transaction->getOut();

		if(($id = $out->getNamedTag()->getString("auctionID", "")) !== ""){
			Await::f2c(function() use($id, $out, $player) {
				if(!$player->isOnline()) return;

				$success = yield AuctionHouseHandler::getInstance()->removeAuction($id);


				if($success === true){
					if($this->getMenu()->getInventory()->contains($out)){
						$this->getMenu()->getInventory()->remove($out);

						$out->getNamedTag()->removeTag("auctionID");
						$out->getNamedTag()->removeTag("menuItem");
						$lore = $out->getLore();
						for($i = 0; $i <= 5; $i++){
							array_pop($lore);
						}

						$out->setLore($lore);
						Utils::addItem($player, $out);
					}
				}
			});
		}
	}
}