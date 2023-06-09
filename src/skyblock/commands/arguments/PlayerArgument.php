<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class PlayerArgument extends StringEnumArgument {



	public function parse(string $argument, CommandSender $sender){
		return Server::getInstance()->getPlayerExact(strtolower($argument));
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		$arr = array_map(fn(Player $player) => $player->getName(), Server::getInstance()->getOnlinePlayers());
		//this does not work don't use it

		return $arr;
	}
}