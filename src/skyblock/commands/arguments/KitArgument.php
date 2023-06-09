<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\misc\kits\Kit;
use skyblock\misc\kits\KitHandler;

class KitArgument extends StringEnumArgument {

	private array $values = [
		"traveler" => Kit::KIT_TRAVELER,
		"sinon" => Kit::KIT_SINON,
		"troje" => Kit::KIT_TROJE,
		"hydra" => Kit::KIT_HYDRA,
		"aurora" => Kit::KIT_AURORA,
		"aether" => Kit::KIT_AETHER,
		"aether+" => Kit::KIT_AETHER_PLUS,
		"astronomical" => Kit::KIT_ASTRONOMICAL,
		"theseus" => Kit::KIT_THESEUS,
		"youtuber" => Kit::KIT_YOUTUBER,
	];

	public function parse(string $argument, CommandSender $sender){
		return KitHandler::getInstance()->get($this->values[strtolower($argument)]);
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_keys($this->values);
	}
}