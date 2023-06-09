<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\items\customenchants\CustomEnchantFactory;
use skyblock\items\customenchants\BaseCustomEnchant;
use skyblock\items\customenchants\CustomEnchantHandler;

class CustomEnchantArgument extends StringEnumArgument {



	public function parse(string $argument, CommandSender $sender){
		return CustomEnchantFactory::getInstance()->getList()[strtolower($argument)];
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_keys(array_map(fn(BaseCustomEnchant $ce) => $ce->getIdentifier()->getName(), CustomEnchantFactory::getInstance()->getList()));
	}
}