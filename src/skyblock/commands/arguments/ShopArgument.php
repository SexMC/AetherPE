<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\misc\shop\Shop;

class ShopArgument extends StringEnumArgument {



	public function parse(string $argument, CommandSender $sender){
		return Shop::getInstance()->getAllItemsByName()[strtolower($argument)] ?? null;
	}

    public function canParse(string $testString, CommandSender $sender): bool {
        return isset(Shop::getInstance()->getAllItemsByName()[strtolower($testString)]);
    }

    public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return strtolower($string);
	}

	public function getEnumValues(): array {
		return array_keys(Shop::getInstance()->getAllItemsByName());
	}
}