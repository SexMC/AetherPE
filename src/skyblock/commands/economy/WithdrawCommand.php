<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\IntTag;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\special\types\MoneyNoteItem;
use skyblock\logs\LogHandler;
use skyblock\logs\types\WithdrawLog;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class WithdrawCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new IntegerArgument("amount"));
		$this->setDescription("Withdraw money into a galaxy note");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$amount = $args["amount"];

			if($amount > 0 && $amount < 2000000000){
				$session = new Session($sender);

				if($session->getPurse() < $amount){
					$sender->sendMessage(Main::PREFIX . "You cannot withdraw more money than you have");
					return;
				}

				$session->decreasePurse($amount);
				Utils::addItem($sender, MoneyNoteItem::getItem($amount, $sender->getName()));
				$sender->sendMessage(Main::PREFIX . "Withdrawn §c$" . number_format($amount));

				LogHandler::getInstance()->log(new WithdrawLog($sender, $amount));
			} else $sender->sendMessage(Main::PREFIX . "Amount must be between 1 and " . number_format(2000000000));
		}
	}
}