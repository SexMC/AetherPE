<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\menus\commands\ArmorInvseeMenu;
use skyblock\menus\commands\EnderInvseeMenu;
use skyblock\menus\commands\InvseeMenu;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class ArmorInvseeCommand extends AetherCommand {


	protected function prepare() : void{
		$this->setPermission("skyblock.command.cinv");

		$this->registerArgument(0, new RawStringArgument("player"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player) {
			$s = new Session($args["player"]);

			if(!$s->playerExists()) {
				$sender->sendMessage(Main::PREFIX . "Invalid player name");
				return;
			}

			if(Utils::isOnline($args["player"])){
				$sender->sendMessage(Main::PREFIX . "That player is online");
				return;
			}

			(new ArmorInvseeMenu($s))->send($sender);
		}
	}
}