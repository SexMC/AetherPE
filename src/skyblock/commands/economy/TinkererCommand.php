<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\menus\commands\TinkererMenu;

class TinkererCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("Trade with the AetherPE Tinkerer");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			(new TinkererMenu())->send($sender);
		}
	}
}