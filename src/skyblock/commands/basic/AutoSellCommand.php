<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class AutoSellCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Autosell");
		$this->setPermission("skyblock.command.autosell");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			$s = new Session($sender);

			if($s->getAutoSell()){
				$s->setAutoSell(false);
				$sender->autosell = false;

				$sender->sendMessage(Main::PREFIX . "Disabled auto sell");
				return;
			}

			$s->setAutoSell(true);
			$sender->autosell = true;
			$sender->sendMessage(Main::PREFIX . "Enabled auto sell");
		}
	}
}