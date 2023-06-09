<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Main;

class ConsoleMsgCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.commands.consolemsg");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new RawStringArgument("text1", false));


		for($i = 2; $i <= 100; $i++){
			$this->registerArgument($i, new RawStringArgument("text$i", true));
		}
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$p = $sender->getServer()->getPlayerExact($args["player"]);

		if($p === null){
			$sender->sendMessage(Main::PREFIX . "Invalid Player");
			return;
		}

		unset($args["player"]);
		$p->sendMessage(implode(" ", $args));
	}
}