<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\BooleanArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;

class ClearlagCommand extends AetherCommand{

	protected function prepare() : void{
		$this->setPermission("skyblock.commands.clearlag");
		$this->setDescription("Clearlag");

		$this->registerArgument(0, new BooleanArgument("instant", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		//Clearlag is being removed
	}
}