<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\menus\commands\auctionhouse\AuctionHouseMenu;
use skyblock\misc\auctionhouse\AuctionHouseHandler;
use skyblock\misc\auctionhouse\AuctionHouseItem;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class AuctionHouseCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new IntegerArgument("sell price", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$amount = $args["sell price"] ?? null;

			if($amount !== null){
				if($amount <= 0) {
					$sender->sendMessage(Main::PREFIX . "Sell price must be greater than 0");
					return;
				}

				if($amount >= 950000000){
					$sender->sendMessage(Main::PREFIX . "Amount must be less than 950 million");
					return;
				}

				$max = 3;

				if($amount >= PHP_INT_MAX){
					$sender->sendMessage(Main::PREFIX . "Sell price is too big!");
					return;
				}


				Await::f2c(function() use($amount, $sender){
					$count = yield AuctionHouseHandler::getInstance()->getAuctionCountByPlayer($sender->getName());
					if(!$sender->isOnline()) return;

					if($count >= 3) {
						$sender->sendMessage(Main::PREFIX . "You cannot auction more than 3 items");
						return;
					}

					$item = $sender->getInventory()->getItemInHand();

					if($item->isNull()){
						$sender->sendMessage(Main::PREFIX . "Please hold a valid item");
						return;
					}

					$sender->getInventory()->clear($sender->getInventory()->getHeldItemIndex());

					$auction = new AuctionHouseItem($sender->getName(), $item->jsonSerialize(), $amount, time());
					$success = yield AuctionHouseHandler::getInstance()->addItem($auction);

					if($success === true){
						$sender->sendMessage(Main::PREFIX . "Successfully auctioned §c{$item->getName()}§7 for §c$" . number_format($auction->getPrice()));
						return;
					}

					Utils::addItem($sender, $item);
					$sender->sendMessage(Main::PREFIX . "Failed to auction your item");
				});

				return;
			}

			Await::f2c(function() use($sender){
				(new AuctionHouseMenu($sender, yield AuctionHouseHandler::getInstance()->getAllAuctions()))->send($sender);
			});
		}
	}
}