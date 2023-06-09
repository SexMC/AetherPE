<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\traits\PlayerCooldownTrait;

class VaccineCommand extends AetherCommand {
	use PlayerCooldownTrait;

	protected function prepare() : void{
		$this->setDescription("Remove bad effects");

		$this->setPermission("skyblock.command.vaccine");
		$this->setAliases(["bless"]);
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){

			if($this->isOnCooldown($sender)){
				$sender->sendMessage(Main::PREFIX . "This command is on cooldown for " . ($this->getCooldown($sender) . " second(s)"));
				return;
			}
			foreach ($sender->getEffects()->all() as $effect){
				if($effect->getType()->isBad()){
					$sender->getEffects()->remove($effect->getType());
				}
			}

			$sender->sendMessage(Main::PREFIX . "You've successfully {$aliasUsed}ed yourself");
		}
	}
}