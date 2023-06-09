<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\lootbox\LootboxHandler;
use skyblock\Main;

class FindCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.find");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$i = $sender->getInventory()->getItemInHand();

			$found = [];
			foreach(LootboxHandler::getInstance()->getLootboxes() as $lootbox){
				foreach($lootbox->getNonDuplicatedAll() as $l){
					if($l->getItem()->equals($i)){
						$found[] = $lootbox::getName();
					}
				}
			}

			if(empty($found)){
				$sender->sendMessage(Main::PREFIX . "Item not found");
			} else foreach(array_unique($found) as $v){
				$sender->sendMessage(Main::PREFIX . "In: §c$v");
			}
		}
	}
}