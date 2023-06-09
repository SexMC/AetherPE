<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\masks\Mask;
use skyblock\items\masks\MasksHandler;
use skyblock\menus\common\ViewPagedItemsMenu;

class MasksCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View all masks");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;


		((new ViewPagedItemsMenu("Masks", array_map(fn(Mask $mask) => $mask::getItem(), MasksHandler::getInstance()->getAllMasks()))))->send($sender);
	}
}