<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\caches\skin\SkinCache;

class SkinsArgument extends StringEnumArgument {



	public function parse(string $argument, CommandSender $sender){
		return SkinCache::getInstance()->getSkin(strtolower($argument));
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return  array_keys(SkinCache::getInstance()->getCache());
	}
}