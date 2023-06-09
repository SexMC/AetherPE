<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\items\itemmods\ItemMod;
use skyblock\items\itemmods\ItemModHandler;

class ItemModArgument extends StringEnumArgument {

	public function parse(string $argument, CommandSender $sender){
		return ItemModHandler::getInstance()->getAllItemMods()[strtolower($argument)];
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_values(array_map(fn(ItemMod $mod) => strtolower($mod::getUniqueID()), ItemModHandler::getInstance()->getAllItemMods()));
	}
}