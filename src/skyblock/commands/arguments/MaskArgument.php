<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\items\masks\MasksHandler;

class MaskArgument extends StringEnumArgument {

	public function parse(string $argument, CommandSender $sender){
		return MasksHandler::getInstance()->getAllMasks()[strtolower($argument)];
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_keys(MasksHandler::getInstance()->getAllMasks());
	}
}