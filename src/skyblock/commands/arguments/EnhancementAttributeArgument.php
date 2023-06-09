<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\items\itemattribute\EnhancementAttributeHandler;
use skyblock\items\itemattribute\EnhancementAttribute;

class EnhancementAttributeArgument extends StringEnumArgument {

	public function parse(string $argument, CommandSender $sender){
		return EnhancementAttributeHandler::getInstance()->getAll()[strtolower($argument)];
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_values(array_map(fn(EnhancementAttribute $mod) => strtolower($mod::getUniqueID()), EnhancementAttributeHandler::getInstance()->getAll()));
	}
}