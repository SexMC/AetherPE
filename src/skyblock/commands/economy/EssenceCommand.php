<?php

declare(strict_types=1);

namespace skyblock\commands\economy;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\economy\sub\EssenceShopSubCommand;
use skyblock\Main;
use skyblock\sessions\Session;

class EssenceCommand extends AetherCommand{
	protected function prepare() : void{
		$this->setDescription("View your/others essence");

		$this->registerArgument(0, new RawStringArgument("player", true));
		$this->registerSubCommand(new EssenceShopSubCommand("shop", ["store"]));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$username = strtolower(($args["player"] ?? $sender->getName()));

		$session = new Session($username);

		if($session->playerExists()){
			$sender->sendMessage(Main::PREFIX . "$username's essence: §c" . number_format($session->getEssence()));
		} else $sender->sendMessage(Main::PREFIX . "No player named §c$username §7was found");
	}
}