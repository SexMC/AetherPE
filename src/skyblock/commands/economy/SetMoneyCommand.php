<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\communication\operations\server\ServerMessageOperation;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class SetMoneyCommand extends AetherCommand{
	protected function prepare() : void{
		$this->setPermission("skyblock.command.setmoney");

		$this->setDescription("Set players money");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new IntegerArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$username = $args["player"];
		$amount = $args["amount"];
		$p = Server::getInstance()->getPlayerExact($username);
		if($p instanceof AetherPlayer){
			$session = $p->getCurrentProfilePlayerSession();
			$session->setPurse($amount);
			$sender->sendMessage(Main::PREFIX . "set §c{$username}§7's money to §c" . number_format($amount));
			Utils::sendMessage($username, Main::PREFIX . "§7Your money has been set to §c" . number_format($amount));
		} else {
			$sender->sendMessage(Main::PREFIX . "No player named §c$username §7was found");
		}
	}
}