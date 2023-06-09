<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use skyblock\commands\AetherCommand;
use skyblock\commands\staff\sub\zone\ZoneCreateSubCommand;
use skyblock\commands\staff\sub\zone\ZoneDeleteCommand;
use skyblock\commands\staff\sub\zone\ZoneInfoCommand;
use skyblock\commands\staff\sub\zone\ZoneListSubCommand;

class ZoneCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.commands.zone");

		$this->registerSubCommand(new ZoneListSubCommand("list"));
		$this->registerSubCommand(new ZoneDeleteCommand("delete"));
		$this->registerSubCommand(new ZoneInfoCommand("info"));

		$this->registerSubCommand(new ZoneCreateSubCommand("create"));
	}
}