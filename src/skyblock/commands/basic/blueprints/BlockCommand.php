<?php

declare(strict_types=1);

namespace skyblock\commands\basic\blueprints;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\AetherSubCommand;

//this is a waterdogpe proxy registered commands
//registering it here so it shows as valid command to the player
class BlockCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerSubCommand(new BlueprintSubCommand("list"));

		$this->setDescription("Blocks a player from sending private messages to you");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{}
}