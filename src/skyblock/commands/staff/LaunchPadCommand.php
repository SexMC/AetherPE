<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use skyblock\commands\AetherCommand;
use skyblock\commands\staff\sub\launchpad\LaunchpadCreateSubCommand;
use skyblock\commands\staff\sub\launchpad\LaunchpadListSubCommand;
use skyblock\commands\staff\sub\launchpad\LaunchpadRemoveSubCommand;

class LaunchPadCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.teleportlocation");

		$this->registerSubCommand(new LaunchpadCreateSubCommand("create"));
		$this->registerSubCommand(new LaunchpadListSubCommand("list"));
		$this->registerSubCommand(new LaunchpadRemoveSubCommand("remove"));
	}
}