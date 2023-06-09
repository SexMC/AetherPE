<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\communication\operations\server\ServerMessageOperation;
use skyblock\Main;
use skyblock\sessions\Session;

class SetXpCommand extends AetherCommand{
	protected function prepare() : void{
		$this->setPermission("skyblock.command.setxp");

		$this->setDescription("Set players xp");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new IntegerArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$username = $args["player"];
		$amount = $args["amount"];

		$session = new Session($username);
		$p = Server::getInstance()->getPlayerExact($username);

		if($p instanceof Player){
			$p->getXpManager()->setCurrentTotalXp($amount);
			$sender->sendMessage(Main::PREFIX . "set §c{$username}§7's xp to §c" . number_format($amount));
			$p->sendMessage(Main::PREFIX . "Your xp has been set to §c" . number_format($amount));
		} else $sender->sendMessage(Main::PREFIX . "No online player named §c$username §7was found");
	}
}