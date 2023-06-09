<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\forms\commands\ItemForm;

class ItemCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("View your item in a form interface");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			if($sender->getInventory()->getItemInHand()->isNull()){
				$sender->sendMessage("§cPlease hold an item");
				return;
			}

			$sender->sendForm(new ItemForm($sender->getInventory()->getItemInHand()));
		}
	}
}