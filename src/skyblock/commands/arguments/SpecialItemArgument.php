<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\SpecialItemHandler;

class SpecialItemArgument extends StringEnumArgument {

	public function parse(string $argument, CommandSender $sender){
		return SpecialItemHandler::getItem(strtolower($argument));
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return  array_values(array_map(fn(SpecialItem $set) => strtolower($set::getItemTag()), SpecialItemHandler::getItems()));
	}
}