<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\Database;
use skyblock\Main;

class AgeStartSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.age");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$time = (int) (Database::getInstance()->redisGet("server.age") ?? 0);

		if($time === 0){
			Database::getInstance()->redisSet("server.age", time());
			$sender->sendMessage(Main::PREFIX . "Started planet countdown");
		} else $sender->sendMessage(Main::PREFIX . "Planet countdown has already started");
	}
}