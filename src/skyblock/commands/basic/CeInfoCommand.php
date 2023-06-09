<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\forms\commands\ceinfo\CeInfoForm;

class CeInfoCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View custom enchants");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$sender->sendForm(new CeInfoForm());
		}
	}
}