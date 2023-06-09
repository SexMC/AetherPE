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

class RankListSubCommand extends AetherSubCommand  {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$session = new Session($args["player"]);

		if($session->playerExists()){
			$ranks = $session->getRanks();

			$sender->sendMessage(Main::PREFIX . "§c{$args["player"]}§7's ranks:");
			foreach($ranks as $rank){
				$sender->sendMessage($rank->getColour() . $rank->getName() . "§r§7 perm: " . ($rank->isPerm() ? "§aYES" : "§cNO"));
			}

		} else $sender->sendMessage(Main::PREFIX . "Player named §c{$args["player"]} was not found");
	}
}