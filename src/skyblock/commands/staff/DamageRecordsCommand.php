<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\player\AetherPlayer;

class DamageRecordsCommand extends AetherCommand {
	public function canBeUsedInCombat() : bool{
		return true;
	}

	protected function prepare() : void{
		$this->setDescription("skyblock.command.damagerecords");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			/*if(($event = $sender->getLastCustomEntityDamageByEntityEvent())){
				$sender->sendMessage((string) $event);
			} else $sender->sendMessage(Main::PREFIX . "No last damage records found");*/

			//TODO: update this
			$sender->sendMessage(Main::PREFIX . "Needs updating");
		}
	}
}