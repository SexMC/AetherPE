<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\lootbox\types\store\PlanetNeptuneLootbox;
use skyblock\menus\commands\PreviewMenu;

class PreviewCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View the current lootbox");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			(new PreviewMenu())->send($sender);
		}
	}
}