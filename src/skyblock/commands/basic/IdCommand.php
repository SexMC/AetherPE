<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;

class IdCommand extends AetherCommand {
	protected function prepare() : void{

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$item = $sender->getInventory()->getItemInHand();

			$sender->sendMessage(Main::PREFIX . $item->getId() . ":" . $item->getMeta());
		}
	}
}