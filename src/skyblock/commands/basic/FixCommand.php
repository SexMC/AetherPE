<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\item\Durable;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\FixAllSubCommand;
use skyblock\Main;
use skyblock\traits\PlayerCooldownTrait;

class FixCommand extends AetherCommand {

	public function canBeUsedInCombat() : bool{
		return true;
	}

	use PlayerCooldownTrait;

	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("hand", true));
		$this->registerSubCommand(new FixAllSubCommand("all"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			if($this->isOnCooldown($sender)){
				$sender->sendMessage(Main::PREFIX . "This command is on cooldown for " . ($this->getCooldown($sender) . " second(s)"));
				return;
			}


			$item = $sender->getInventory()->getItemInHand();
			if($item instanceof Durable){
				$this->setCooldown($sender, 30);
				$item->setDamage(0);
				$sender->getInventory()->setItemInHand($item);
				$sender->sendMessage(Main::PREFIX . "Fixed the item you're holding");
			} else $sender->sendMessage(Main::PREFIX . "This item cannot be fixed");
		}
	}
}