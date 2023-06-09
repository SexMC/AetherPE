<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\shop\Shop;

class SellHandSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("Sell the item in your hand");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$item = $sender->getInventory()->getItemInHand();

			$gain = Shop::getInstance()->sellItem($sender, $item);

			if($gain <= 0){
				$sender->sendMessage(Main::PREFIX . "The item in your hand is not sellable");
				return;
			}

			$sender->getInventory()->clear($sender->getInventory()->getHeldItemIndex());
			$sender->sendMessage(Main::PREFIX . "Sold §c{$item->getCount()}x {$item->getName()} §7for §c$" . number_format($gain));
		}
	}
}