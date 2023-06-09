<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\island;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\forms\island\IslandInfoForm;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class IslandInfoSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("See island information.");
		$this->registerArgument(0, new RawStringArgument("island/player", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			$session = new Session($sender);

			if(!isset($args["island/player"])){
				$island = $session->getIslandOrNull();

				if($island === null){
					$sender->sendMessage(Main::PREFIX . "Please provide an island or player name");
					return;
				}

				$sender->sendForm(new IslandInfoForm($island));

				return;
			}

			$looking = $args["island/player"];

			$s = new Session($looking);
			if($s->playerExists()){
				if(($island = $s->getIslandOrNull())){
					$sender->sendForm(new IslandInfoForm($island));
				} else $sender->sendMessage(Main::PREFIX . "§c$looking §7is not in an island");
			} elseif(($island = new Island($looking))->exists()){
				$sender->sendForm(new IslandInfoForm($island));
			} else $sender->sendMessage(Main::PREFIX . "No island or player named §c$looking §7was found");
		}
	}
}