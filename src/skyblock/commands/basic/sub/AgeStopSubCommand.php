<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\Database;
use skyblock\Main;

class AgeStopSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.age");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		Database::getInstance()->redisSet("server.age", 0);
		$sender->sendMessage(Main::PREFIX . "Successfully reset the planet countdown");
	}
}