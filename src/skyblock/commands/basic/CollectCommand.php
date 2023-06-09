<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\menus\commands\CollectMenu;
use skyblock\sessions\Session;
use skyblock\traits\StringCooldownTrait;

class CollectCommand extends AetherCommand {
	use StringCooldownTrait;

	protected function prepare() : void{
		$this->setDescription("Collect your items");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			if($this->isOnCooldown($sender->getName())){
				$sender->sendMessage(Main::PREFIX . "This command is on cooldown for §c" . $this->getCooldown($sender->getName()) . " §7second(s)");
				return;
			}

			$this->setCooldown($sender->getName(), 20);

			(new CollectMenu(new Session($sender)))->send($sender);
		}
	}
}