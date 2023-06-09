<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\DefinedStringArgument;
use skyblock\items\tools\SpecialWeapon;
use skyblock\items\tools\SpecialWeaponHandler;
use skyblock\Main;
use skyblock\utils\Utils;

class SpecialToolCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.specialtool");
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new DefinedStringArgument(SpecialWeaponHandler::getInstance()->getList(), "tool"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$p = Server::getInstance()->getPlayerExact($args["player"]);

		if($p === null){
			$sender->sendMessage(Main::PREFIX . "Invalid Player");
			return;
		}

		/** @var SpecialWeapon $tool */
		$tool = $args["tool"];

		Utils::addItem($sender, $tool::getItem());
		$sender->sendMessage(Main::PREFIX . "Gave §c" . $args["player"] . " " . $tool::getName());
	}
}