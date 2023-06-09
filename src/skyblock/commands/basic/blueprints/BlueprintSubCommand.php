<?php

declare(strict_types=1);

namespace skyblock\commands\basic\blueprints;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;

class BlueprintSubCommand extends AetherSubCommand {
	protected function prepare() : void{}
	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{}
}