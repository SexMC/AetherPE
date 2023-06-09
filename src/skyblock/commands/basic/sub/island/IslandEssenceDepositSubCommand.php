<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\island;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\logs\LogHandler;
use skyblock\logs\types\BankLog;
use skyblock\Main;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class IslandEssenceDepositSubCommand extends AetherSubCommand{
	protected function prepare() : void{
		$this->setDescription("Deposit essence into your island bank");
		$this->registerArgument(0, new IntegerArgument("essence_amount"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			$session = new Session($sender);

			$island = $session->getIslandOrNull();

			if($island === null){
				$sender->sendMessage(Main::PREFIX . "You're not in an island");
				return;
			}

			$amount = abs($args["essence_amount"]);

			if($amount <= 0){
				$sender->sendMessage(Main::PREFIX . "Amount must be greater than 0");
				return;
			}


			if($session->getEssence() < $amount){
				$sender->sendMessage(Main::PREFIX . "You cannot deposit more essence than you have!");
				return;
			}

			$session->decreaseEssence($amount);
			$island->increaseBankEssence($amount);

			$format = number_format($amount);
			$island->announce(Main::PREFIX . "§c{$sender->getName()}§7 has deposited §c" . $format . " ESSENCE §7into the island bank.");
			LogHandler::getInstance()->log(new BankLog($sender, $island, $amount, "ESSENCE"));
			QuestHandler::getInstance()->increaseProgress(IQuest::BANK_DEPOSIT, $sender, $session, $amount);
		}
	}
}