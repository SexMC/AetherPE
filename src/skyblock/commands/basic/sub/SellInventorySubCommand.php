<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\shop\Shop;

class SellInventorySubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("Sell the items in your inventory");
		$this->setPermission("skyblock.command.sellinventory");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			$gain = Shop::getInstance()->sellInventory($sender, $sender->getInventory());

			if($gain <= 0){
				$sender->sendMessage(Main::PREFIX . "Nothing sellable found in your inventory");
				return;
			}

			$sender->sendMessage(Main::PREFIX . "Sold all sellable items in your inventory for §c$" . number_format($gain));
		}
	}
}