<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\misc\warpspeed\IWarpSpeed;
use skyblock\misc\warpspeed\WarpSpeedHandler;

class FundCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Fund");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$sender->sendMessage(Main::PREFIX . "§cThe intergalactic species have managed to lock this command.");
	}
}