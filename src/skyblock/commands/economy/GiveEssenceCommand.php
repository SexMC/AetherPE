<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\communication\operations\server\ServerMessageOperation;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class GiveEssenceCommand extends AetherCommand{
	protected function prepare() : void{
		$this->setPermission("skyblock.command.giveessence");

		$this->setDescription("Give essence to player");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new IntegerArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$username = str_replace('"', '', $args["player"]);
		$amount = $args["amount"];

		$session = new Session($username);

		if($session->playerExists()){
			$session->increaseEssence($amount, false);
			$sender->sendMessage(Main::PREFIX . "§c{$username}§7's essence has been increased by §c" . number_format($amount));
			Utils::sendMessage($username, Main::PREFIX . "§7Your essence has been increased by §c" . number_format($amount));
		} else $sender->sendMessage(Main::PREFIX . "No player named §c$username §7was found");
	}
}