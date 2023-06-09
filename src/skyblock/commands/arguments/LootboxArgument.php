<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\items\lootbox\Lootbox;
use skyblock\items\lootbox\LootboxHandler;

class LootboxArgument extends StringEnumArgument {

	public function parse(string $argument, CommandSender $sender){
		return LootboxHandler::getInstance()->getLootbox(strtolower($argument));
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_values(array_map(fn(Lootbox $lootbox) => strtolower($lootbox::getName()), LootboxHandler::getInstance()->getLootboxes()));
	}
}