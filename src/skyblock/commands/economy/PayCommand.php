<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\communication\operations\server\ServerMessageOperation;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class PayCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Pay money to your friends");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new IntegerArgument("amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$session = new Session($sender);
			$username = strtolower($args["player"]);
			$amount = $args["amount"];

			if($amount <= 0){
				$sender->sendMessage(Main::PREFIX . "Amount must be greater than 0");
				return;
			}

			if(in_array($username, Utils::getOnlinePlayerUsernames())){
				if($amount > $session->getPurse()){
					$sender->sendMessage(Main::PREFIX . "You cannot pay more money than you have");
					return;
				}

				if($username === strtolower($sender->getName())){
					$sender->sendMessage(Main::PREFIX . "You cannot pay yourself");
					return;
				}

				$session->decreasePurse($amount);
				(new Session($username))->increasePurse($amount);

				$sender->sendMessage(Main::PREFIX . "Paid §c$" . number_format($amount) . "§7 to §c" . $username);
				Utils::sendMessage($username, Main::PREFIX . "§c{$sender->getName()}§7 has paid you §c$" . number_format($amount));
			} else $sender->sendMessage(Main::PREFIX . "No online player named $username was found");
		}
	}
}