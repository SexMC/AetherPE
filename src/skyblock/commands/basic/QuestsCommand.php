<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\menus\quests\RegularQuestsMenu;
use skyblock\sessions\Session;

class QuestsCommand extends AetherCommand{

	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player", true));

		$this->setDescription("Player Quests");
		$this->setAliases(["quest"]);
		
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			(new RegularQuestsMenu($sender, new Session($sender)))->send($sender);
		}
	}
}