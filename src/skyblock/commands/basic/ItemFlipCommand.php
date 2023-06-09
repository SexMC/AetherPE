<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\menus\itemflip\ItemFlipMenu;

class ItemFlipCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Flip your items");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;


		(new ItemFlipMenu($sender, $sender))->send($sender);
	}
}