<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\quests\DailyQuestInstance;
use skyblock\misc\quests\QuestHandler;
use skyblock\player\AetherPlayer;

class DailyQuestsResetSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("Reset players daily quests");
		$this->setPermission("skybloc.command.dailyquests.reset");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new RawStringArgument("quest"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$p = $sender->getServer()->getPlayerExact($args["player"]);

		if(!$p instanceof AetherPlayer) {
			$sender->sendMessage(Main::PREFIX . "Invalid player");
			return;
		}

		$removed = false;
		foreach($p->quests as $id => $list){
			foreach($list as $key => $quest){
				if($quest instanceof DailyQuestInstance) {
					if(strtolower($quest->quest->name) === strtolower($args["quest"])){
						unset($p->quests[$id][$key]);
						QuestHandler::getInstance()->checkDailyQuests($p);

						$removed = true;
					}
				}
			}
		}

		if($removed){
			$sender->sendMessage(Main::PREFIX . "Removed §c{$args["quest"]} §7from§c {$p->getName()}");
		} else $sender->sendMessage(Main::PREFIX . "§c{$p->getName()}§7 doesn't have a quest named §c{$args["quest"]}");
	}
}