<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\traits\PlayerCooldownTrait;

class HealCommand extends AetherCommand {
	use PlayerCooldownTrait;

	protected function prepare() : void{
		$this->setDescription("Heal yourself");
		$this->setPermission("skyblock.command.heal");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			if($this->isOnCooldown($sender)){
				$sender->sendMessage(Main::PREFIX . "This command is on cooldown for " . ($this->getCooldown($sender) . " second(s)"));
				return;
			}
			$this->setCooldown($sender, 30);
			$sender->setHealth($sender->getMaxHealth());
			$sender->sendMessage(Main::PREFIX . "You have successfully healed yourself");
		}
	}
}