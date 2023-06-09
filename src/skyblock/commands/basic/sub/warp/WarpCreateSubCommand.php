<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\warp;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\warps\Warp;
use skyblock\utils\Utils;

class WarpCreateSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.warp.create");
		$this->registerArgument(0, new RawStringArgument("name"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$name = $args["name"];
			$warp = new Warp(
				$name,
				$sender->getPosition()->getWorld()->getDisplayName(),
				Utils::getServerName(),
				$sender->getPosition()->asVector3(),
				true
			);

			$warp->save();

			$sender->sendMessage(Main::PREFIX . "Created warp named§c $name");
		}
	}
}