<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\menus\collection\CollectionMenu;
use skyblock\player\AetherPlayer;

class CollectionCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View your collection");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			(new CollectionMenu($sender))->send($sender);
		}
	}
}