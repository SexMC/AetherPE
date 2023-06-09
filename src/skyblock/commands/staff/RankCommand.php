<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\RankArgument;
use skyblock\commands\staff\sub\RankAddSubCommand;
use skyblock\commands\staff\sub\RankListSubCommand;
use skyblock\commands\staff\sub\RankRemoveSubCommand;
use skyblock\Main;
use skyblock\sessions\Session;

class RankCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.rank");
		$this->setDescription("Set ranks");

		$this->registerSubCommand(new RankAddSubCommand("add"));
		$this->registerSubCommand(new RankListSubCommand("list"));
		$this->registerSubCommand(new RankRemoveSubCommand("remove"));

        $this->registerArgument(0, new RawStringArgument("username", true));
        $this->registerArgument(1, new RankArgument("rank", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
	}
}