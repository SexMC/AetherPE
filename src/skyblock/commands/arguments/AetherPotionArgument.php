<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\items\potions\AetherPotionHandler;

class AetherPotionArgument extends StringEnumArgument {

	public function parse(string $argument, CommandSender $sender){
		return AetherPotionHandler::getInstance()->getPotion(strtolower($argument));
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		//var_dump(array_keys(AetherPotionHandler::getInstance()->getPotions()));
		return array_keys(AetherPotionHandler::getInstance()->getPotions());
	}
}