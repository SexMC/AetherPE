<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\caches\playtime\PlayTimeCache;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\TimeUtils;

class PlayTimeCommand extends AetherCommand {

	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player", true));

		$this->setDescription("View your playtime");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$name = $args["player"] ?? $sender->getName();

			$session = new Session($name);
			if($session->playerExists()){
				$total = $session->getPlayTime() + PlayTimeCache::getInstance()->get($sender->getName());
				$sender->sendMessage(Main::PREFIX . "Playtime of $name: §c" . TimeUtils::getFormattedTime($total));
			} else $sender->sendMessage(Main::PREFIX . "No player named §c$name §7was found");
		}
	}
}