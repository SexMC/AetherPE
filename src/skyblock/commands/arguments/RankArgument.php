<?php

declare(strict_types=1);

namespace skyblock\commands\arguments;

use CortexPE\Commando\args\StringEnumArgument;
use pocketmine\command\CommandSender;
use skyblock\player\ranks\BaseRank;
use skyblock\player\ranks\RankHandler;

class RankArgument extends StringEnumArgument {



	public function parse(string $argument, CommandSender $sender){
		return RankHandler::getInstance()->getRank(strtolower($argument));
	}

	public function getTypeName() : string{
		return "string";
	}

	public function getValue(string $string) {
		return $string;
	}

	public function getEnumValues(): array {
		return array_values(array_map(fn(BaseRank $rank) => strtolower($rank->getName()), RankHandler::getInstance()->getRanks()));
	}
}