<?php

declare(strict_types=1);

namespace skyblock\commands\staff;


use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use skyblock\commands\AetherCommand;
use skyblock\player\AetherPlayer;

class UnicodeCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.commands.unicode");

		$this->registerArgument(0, new RawStringArgument("unicode"));
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		var_dump($args);
		var_dump(intval($args["unicode"]));
		$player->sendMessage("unicode: " . "\u{E1FA}");
		$player->sendMessage("unicode: " . "\u{E1FB}");
		$player->sendMessage("unicode: " . "\u{E1FC}");
		$player->sendMessage("unicode: " . "\u{E1F9}");
		$player->sendMessage("unicode: " . "\u{E1F8}");
		$player->sendMessage("unicode: " . "\u{E168}");
		$player->sendMessage("unicode: " . "\u{E169}");
		$player->sendMessage("unicode: " . "\u{E160}");
		$player->sendMessage("unicode: " . "\u{E161}");
		$player->sendMessage("unicode: " . "\u{E1AD}");
		$player->sendMessage("unicode: " . "\u{E1AC}");
	}
}