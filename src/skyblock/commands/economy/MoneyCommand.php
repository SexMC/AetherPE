<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\sessions\Session;

class MoneyCommand extends AetherCommand{
	protected function prepare() : void{
		$this->setDescription("View your/others money");

		$this->registerArgument(0, new RawStringArgument("player", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$username = $args["player"] ?? $sender->getName();
		
		$session = new Session($username);
		
		if($session->playerExists()){
			$sender->sendMessage(Main::PREFIX . "$username's balance: §c$" . number_format($session->getPurse()));
		} else $sender->sendMessage(Main::PREFIX . "No player named §c$username §7was found");
	}
}