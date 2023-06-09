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

class RankRemoveSubCommand extends AetherSubCommand  {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new RankArgument("rank"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$session = new Session($args["player"]);

		if($session->playerExists()){
			$removed = $session->removeRank($args["rank"]);

			if(!$removed){
				$sender->sendMessage(Main::PREFIX . "§c{$args["player"]} §7does not have the §c{$args["rank"]->getName()}§7 rank");
				return;
			}

			$sender->sendMessage(Main::PREFIX . "Removed §c{$args["rank"]->getName()}§7 from §c{$args["player"]}");
		} else $sender->sendMessage(Main::PREFIX . "Player named §c{$args["player"]} was not found");
	}
}