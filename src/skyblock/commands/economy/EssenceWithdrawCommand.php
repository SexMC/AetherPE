<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\IntTag;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\special\types\EssenceNoteItem;
use skyblock\items\special\types\MoneyNoteItem;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class EssenceWithdrawCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new IntegerArgument("amount"));
		$this->setDescription("Withdraw essence into a essence note");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$amount = $args["amount"];

			if($amount > 0 && $amount < 2000000000){
				$session = new Session($sender);

				if($session->getEssence() < $amount){
					$sender->sendMessage(Main::PREFIX . "You cannot withdraw more essence than you have");
					return;
				}

				$session->decreaseEssence($amount);
				Utils::addItem($sender, EssenceNoteItem::getItem($amount, $sender->getName()));
				$sender->sendMessage(Main::PREFIX . "Withdrawn §c" . number_format($amount) . " essence");
			} else $sender->sendMessage(Main::PREFIX . "Amount must be between 1 and " . number_format(2000000000));
		}
	}
}