<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\MaskArgument;
use skyblock\items\masks\Mask;
use skyblock\Main;
use skyblock\utils\Utils;

class MaskCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setPermission("skyblock.command.mask");
		$this->setDescription("Give masks");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new MaskArgument("mask"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			/** @var Mask $mask */
			$mask = $args["mask"];
			if(!$mask instanceof Mask){
				$sender->sendMessage(Main::PREFIX . "error");
				return;
			}


			$player = Server::getInstance()->getPlayerExact($args["player"]);

			if($player === null){
				$sender->sendMessage(Main::PREFIX . "Invalid player");
				return;
			}

			$sender->sendMessage(Main::PREFIX . "Gave {$player->getName()} a {$mask::getName()} mask");
			$player->sendMessage(Main::PREFIX . "You have received a §c{$mask::getName()}§7 mask");
			Utils::addItem($player, $mask::getItem());
		}
	}
}