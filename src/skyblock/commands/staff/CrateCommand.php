<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\DefinedStringArgument;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\server\ExecuteCommandPacket;
use skyblock\items\crates\Crate;
use skyblock\items\crates\CrateHandler;
use skyblock\Main;
use skyblock\utils\Utils;

class CrateCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.crate");
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new DefinedStringArgument(CrateHandler::getInstance()->getAllCrates(), "crate"));
		$this->registerArgument(2, new IntegerArgument("count", true));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$username = str_replace('"', '', $args["player"]);

		if($username === "all"){
			/** @var Crate $crate */
			$crate = $args["crate"];

			CommunicationLogicHandler::getInstance()->sendPacket(new ExecuteCommandPacket(
				array_map(fn(string $p) => "crate \"$p\" {$crate->getName()}", Utils::getOnlinePlayerUsernames())
			));
			Utils::announce(Main::PREFIX . "§c{$sender->getName()}§7 has done a §c{$crate->getName()}§7 key all.");
			return;
		}

		$p = $sender->getServer()->getPlayerExact($username);

		if($p === null){
			$sender->sendMessage(Main::PREFIX . "Invalid player");
			return;
		}

		$count = $args["count"] ?? 1;
		/** @var Crate $crate */
		$crate = $args["crate"];

		Utils::addItem($p, $crate->getKeyItem($count));
		$p->sendMessage(Main::PREFIX . "You have received §c{$count}x {$crate->getName()}§7 crate");
		$sender->sendMessage(Main::PREFIX . "Gave {$p->getName()} {$count}x {$crate->getName()} crate");
	}
}