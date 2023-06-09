<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\FloatArgument;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\SpeedEffect;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;

class SpeedCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new FloatArgument("speed"));

		$this->setPermission("skyblock.command.speed");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$sender->setMovementSpeed($args["speed"]);
			$sender->setSprinting();
		}
	}
}