<?php

declare(strict_types=1);

namespace skyblock\commands\basic\blueprints;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\args\TextArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;

//this is a waterdogpe proxy registered commands
//registering it here so it shows as valid command to the player
class RecallCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new TextArgument("message"));
		$this->setDescription("Send a message to the last player you spoke with");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{

	}
}