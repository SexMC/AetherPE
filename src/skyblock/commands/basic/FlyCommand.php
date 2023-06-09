<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use kingofturkey38\voting38\Main as Voting;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use SOFe\AwaitGenerator\Await;

class FlyCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("Go high into the skies");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;

		Await::f2c(function() use($sender) {
			if(!$sender->hasPermission("skyblock.command.fly")){
				$voted = yield Voting::getInstance()->hasVoted($sender->getName(), false);
				if($voted === false){
					$sender->sendMessage(Main::PREFIX . "You can vote vote.aetherpe.net for 24 hours of free fly!");
					return;
				}
			}

			if(!$sender->isOnline()) return;

			if($sender->getAllowFlight()){
				$sender->sendMessage(Main::PREFIX . "§cDisabled §7fly");
				$sender->setAllowFlight(false);
				$sender->setFlying(false);
			} else {
				$sender->sendMessage(Main::PREFIX . "§aEnabled §7fly");
				$sender->setAllowFlight(true);
				$sender->setFlying(true);
			}
		});

	}
}