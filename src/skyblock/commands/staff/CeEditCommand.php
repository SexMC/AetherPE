<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\forms\commands\CeEditForm;
use skyblock\Main;
use skyblock\player\AetherPlayer;

class CeEditCommand extends AetherCommand {
	public function canBeUsedInCombat() : bool{
		return true;
	}

	protected function prepare() : void{
		$this->setPermission("skyblock.command.ceedit");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			$sender->sendForm((new CeEditForm($sender->getInventory()->getItemInHand())));
		}
	}
}