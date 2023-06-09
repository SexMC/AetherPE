<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\menus\commands\StrongholdMenu;

class StrongholdCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("See Strongholds");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			(new StrongholdMenu())->send($sender);
		}
	}
}
