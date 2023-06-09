<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Database;
use skyblock\Main;

class RealnameCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View the real names of nicked players");
		$this->registerArgument(0, new RawStringArgument("nickname"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$nick = $args["nickname"];
		$v = Database::getInstance()->getRedis()->get("server.nick." . strtolower($nick));

		if($v === null){
			$sender->sendMessage(Main::PREFIX . "No player nicknamed §c$nick §7was found");
			return;
		}

		$sender->sendMessage(Main::PREFIX . "§c$v §7is nicknamed §c$nick");
	}
}