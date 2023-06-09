<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\staff\sub\PvpZoneCreateSubCommand;
use skyblock\commands\staff\sub\PvpZoneDeleteSubCommand;
use skyblock\commands\staff\sub\PvpZoneListSubCommand;

class PvpZoneCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.pvpzone");

		$this->registerSubCommand(new PvpZoneCreateSubCommand("create"));
		$this->registerSubCommand(new PvpZoneDeleteSubCommand("delete"));
		$this->registerSubCommand(new PvpZoneListSubCommand("list"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;
	}
}