<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\forms\commands\ShopBuyForm;
use skyblock\menus\shop\ShopMenu;
use skyblock\misc\shop\Shop;

class ShopCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Shop");

		//$this->registerArgument(0, new ShopArgument("item", true));
		//$this->registerArgument(1, new IntegerArgument("count", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$item = $args["item"] ?? null;
			$count = $args["count"] ?? null;

			if($item !== null){
				if($count === null){
					$sender->sendForm(new ShopBuyForm($item, $sender));
				} else Shop::buy($sender, $item, abs($count));

				return;
			}

			(new ShopMenu())->send($sender);
		}
	}
}