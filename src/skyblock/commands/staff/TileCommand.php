<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\Main;

class TileCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.command.tile");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;

		$block = $sender->getTargetBlock(5);
		$tile = $sender->getWorld()->getTile($block->getPosition());

		if($tile !== null){
			$sender->sendMessage(Main::PREFIX . "Tile class: " . $tile::class);
			return;
		}

		$sender->sendMessage(Main::PREFIX . "No tile found");
	}
}