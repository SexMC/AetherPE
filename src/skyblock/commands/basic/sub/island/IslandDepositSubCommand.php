<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\island;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\item\VanillaItems;
use skyblock\commands\AetherSubCommand;
use skyblock\islands\Island;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\quest\HeroicQuestToken;
use skyblock\items\special\types\quest\QuestToken;
use skyblock\logs\LogHandler;
use skyblock\logs\types\BankLog;
use skyblock\Main;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class IslandDepositSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("Deposit stuff into your island bank");
		$this->registerArgument(0, new IntegerArgument("money_amount", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			$session = new Session($sender);

			$island = $session->getIslandOrNull();

			if($island === null){
				$sender->sendMessage(Main::PREFIX . "You're not in an island");
				return;
			}

			if(isset($args["money_amount"])){
				$amount = abs($args["money_amount"]);

				if($amount <= 0){
					$sender->sendMessage(Main::PREFIX . "Amount must be greater than 0");
					return;
				}


				if($session->getPurse() < $amount){
					$sender->sendMessage(Main::PREFIX . "You cannot deposit more money than you have!");
					return;
				}

				$session->decreasePurse($amount);
				$island->increaseBankMoney($amount);

				$format = number_format($amount);
				$island->announce(Main::PREFIX . "§c{$sender->getName()}§7 has deposited §c$" . $format . " §7into the island bank.");
				LogHandler::getInstance()->log(new BankLog($sender, $island, $amount, "$$$ MONEY"));
				QuestHandler::getInstance()->increaseProgress(IQuest::BANK_DEPOSIT, $sender, $session, $amount);
				return;
			}

			$hand = $sender->getInventory()->getItemInHand();

			$special = $hand->getNamedTag()->getString(SpecialItem::TAG_SPECIAL_ITEM, "");

			if($special === QuestToken::getItemTag()){
				$sender->getInventory()->setItemInHand(VanillaItems::AIR());
				$island->increaseQuestTokens($hand->getCount());
				$island->announce(Main::PREFIX . "§c{$sender->getName()}§7 has deposited §c{$hand->getCount()}x Regular Quest Tokens §7into the island bank.");
				LogHandler::getInstance()->log(new BankLog($sender, $island, $hand->getCount(), "Regular Quest Tokens"));
			} else if($special === HeroicQuestToken::getItemTag()){
				$sender->getInventory()->setItemInHand(VanillaItems::AIR());
				$island->increaseHeroicQuestTokens($hand->getCount());
				$island->announce(Main::PREFIX . "§c{$sender->getName()}§7 has deposited §c{$hand->getCount()}x Heroic Quest Tokens §7into the island bank.");
				LogHandler::getInstance()->log(new BankLog($sender, $island, $hand->getCount(), "Heroic Quest Tokens"));
			} else $sender->sendMessage(Main::PREFIX . "You're not holding a quest token");
		}
	}
}