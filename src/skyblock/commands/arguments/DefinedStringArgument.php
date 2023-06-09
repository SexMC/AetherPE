<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;

class DefinedStringArgument extends StringEnumArgument {

	public function __construct(private array $values, string $name, bool $optional = false){ parent::__construct($name, $optional); }

	public function parse(string $argument, CommandSender $sender){
		return $this->values[strtolower($argument)];
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getEnumValues() : array{
		return array_keys($this->values);
	}
}