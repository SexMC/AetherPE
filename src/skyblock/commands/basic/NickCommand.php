<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use developerdino\profanityfilter\Filter;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\basic\sub\NickRemoveSubCommand;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class NickCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.nick");

		$this->setDescription("Rename yourself");
		$this->registerArgument(0, new RawStringArgument("nickname"));

		$this->registerSubCommand(new NickRemoveSubCommand("remove", ["reset"]));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof AetherPlayer) return;
		$nick = $args["nickname"];

		if (Filter::getInstance()->hasProfanity($nick)){
			$sender->sendMessage(Main::PREFIX . "§c(§l!§r§c) your nickname cannot contain bad words!");
			return;
		}

		foreach(str_split($nick) as $str){
			if(is_numeric($str) or ctype_alpha($str) or $str == " ") {
				continue;
			}

			$sender->sendMessage(Main::PREFIX . "§c(§l!§r§c)§b nickname cannot contain special characters");
			return;
		}

		if(strlen($nick) < 3){
			$sender->sendMessage(Main::PREFIX . "§c(§l!§r§c) nickname must be at least 3 characters long");
			return;
		}

		if(strlen($nick) > 12){
			$sender->sendMessage(Main::PREFIX . "§c(§l!§r§c) nickname must be between 3 and 12 characters");
			return;
		}

		$sender->nick = $args["nickname"];
		(new Session($sender))->setNick($args["nickname"]);
		$sender->sendMessage(Main::PREFIX . "§c(§l!§r§c)§b your nickname is now§d $nick");
	}
}