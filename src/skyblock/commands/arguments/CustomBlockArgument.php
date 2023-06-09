<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\blocks\custom\CustomBlockHandler;

class CustomBlockArgument extends StringEnumArgument {

	public function parse(string $argument, CommandSender $sender){
		return CustomBlockHandler::getInstance()->getBlock(strtolower($argument));
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_keys(CustomBlockHandler::getInstance()->getAllBlocks());
	}
}