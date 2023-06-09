<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\Database;
use skyblock\Main;

class AgeSetSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.age");
		$this->registerArgument(0, new IntegerArgument("seconds"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		Database::getInstance()->getRedis()->set("server.age", time() - $args["seconds"]);

		$sender->sendMessage(Main::PREFIX . "Set age to §c" . number_format($args["seconds"]). "§7 seconds");
	}
}