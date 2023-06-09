<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\menus\commands\WarpSpeedMenu;

class WarpSpeedCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View warp speed");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			(new WarpSpeedMenu())->send($sender);
		}
	}
}