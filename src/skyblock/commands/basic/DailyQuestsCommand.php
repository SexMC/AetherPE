<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\DailyQuestsResetSubCommand;
use skyblock\menus\quests\DailyQuestsMenu;
use skyblock\player\AetherPlayer;

class DailyQuestsCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Daily Quests");

		$this->registerSubCommand(new DailyQuestsResetSubCommand("reset"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof AetherPlayer){
			(new DailyQuestsMenu($sender))->send($sender);
		}
	}
}