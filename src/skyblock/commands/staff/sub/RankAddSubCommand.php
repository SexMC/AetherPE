<?php

declare(strict_types=1);

namespace skyblock\commands\staff\sub;

use CortexPE\Commando\args\BooleanArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\commands\arguments\RankArgument;
use skyblock\Main;
use skyblock\sessions\Session;

class RankAddSubCommand extends AetherSubCommand  {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new RankArgument("rank"));
		$this->registerArgument(2, new BooleanArgument("permanent"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$session = new Session($args["player"]);

		if($session->playerExists()){
			$session->addRank($args["rank"], $args["permanent"]);
			$sender->sendMessage(Main::PREFIX . "§7Gave §c{$args["player"]}§7 rank §c" . $args["rank"]->getName());
		} else $sender->sendMessage(Main::PREFIX . "Player named §c{$args["player"]} was not found");
	}
}