<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class ResetNpcQuestProgressCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.resetnpcprogress");
		$this->registerArgument(0, new RawStringArgument("player"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			if(($p = Server::getInstance()->getPlayerExact($args["player"])) instanceof AetherPlayer){
				assert($p instanceof AetherPlayer);

				$p->getCurrentProfilePlayerSession()->setNpcStarterQuestProgress(0);
				$sender->sendMessage(Main::PREFIX . $args["player"] . "'s npc quest progress has been reset.");
			} else $sender->sendMessage(Main::PREFIX . "That player was not found");
		}
	}
}