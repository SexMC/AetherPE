<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use developerdino\profanityfilter\Filter;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class NickRemoveSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.nick");

		$this->setDescription("Remove your nick");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof AetherPlayer) return;

		$sender->nick = null;
		(new Session($sender))->setNick(null);
		$sender->sendMessage(Main::PREFIX . "Your nick has been reset");
	}
}