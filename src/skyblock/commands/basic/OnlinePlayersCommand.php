<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\utils\Utils;

class OnlinePlayersCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("View all online players");

		$this->registerArgument(0, new RawStringArgument("local", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$all = Utils::getOnlinePlayerUsernames();

		if(isset($args["local"]) && $args["local"] === "local"){
			$all = Utils::getOnlinePlayerUsernamesLocally();
		}

		$count = count($all);
		$sender->sendMessage(Main::PREFIX . "Online players (§c{$count}§7): " . implode(", ", $all));
	}
}