<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\IntegerArgument;
use pocketmine\command\CommandSender;
use pocketmine\nbt\tag\IntTag;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\special\types\MoneyNoteItem;
use skyblock\items\special\types\XPBottleItem;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class XpBottleCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new IntegerArgument("amount"));
		$this->setDescription("Bottle your Experience");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$amount = $args["amount"];

			if($amount > 0 && $amount < 2000000000){
				if($sender->getXpManager()->getCurrentTotalXp() < $amount){
					$sender->sendMessage(Main::PREFIX . "You cannot withdraw more XP than you have");
					return;
				}

				$sender->getXpManager()->setCurrentTotalXp($sender->getXpManager()->getCurrentTotalXp() - $amount);
				Utils::addItem($sender, XPBottleItem::getItem($amount, $sender->getName()));
				$sender->sendMessage(Main::PREFIX . "Withdrawn §c" . number_format($amount) . " XP");
			} else $sender->sendMessage(Main::PREFIX . "Amount must be between 1 and " . number_format(2000000000));
		}
	}
}