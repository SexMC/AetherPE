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

class TakeXpCommand extends AetherCommand{
	protected function prepare() : void{
		$this->setPermission("skyblock.command.takexp");

		$this->setDescription("Set players xp");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new IntegerArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$username = $args["player"];
		$amount = $args["amount"];

		$p = Server::getInstance()->getPlayerExact($username);

		if($p instanceof Player){
			$p->getXpManager()->setCurrentTotalXp(max(0, $p->getXpManager()->getCurrentTotalXp() - $amount));
			$sender->sendMessage(Main::PREFIX . "§c{$username}§7's xp has been decreased by §c" . number_format($amount));
			$p->sendMessage(Main::PREFIX . "Your xp has been decreased by §c" . number_format($amount));
		} else $sender->sendMessage(Main::PREFIX . "No online player named §c$username §7was found");
	}
}