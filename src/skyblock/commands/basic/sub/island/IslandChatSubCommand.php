<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\island;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class IslandChatSubCommand extends AetherSubCommand {

	public function canBeUsedInCombat() : bool{
		return true;
	}

	protected function prepare() : void{
		$this->setDescription("Chat with your island members");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof AetherPlayer) return;
		$session = new Session($sender);

		$island = $session->getIslandOrNull();

		if($island === null){
			$sender->sendMessage(Main::PREFIX . "You're not in an island");
			return;
		}

		if($session->getIslandChat()){
			$session->setIslandChat(false);
			$sender->islandChat = false;
			$sender->sendMessage(Main::PREFIX . "Disabled island chat");
			return;
		}

		$sender->islandChat = true;
		$session->setIslandChat(true);
		$sender->sendMessage(Main::PREFIX . "Enabled island chat");
	}
}