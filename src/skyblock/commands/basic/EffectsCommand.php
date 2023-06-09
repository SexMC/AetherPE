<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use skyblock\commands\AetherCommand;
use skyblock\menus\commands\PotionsMenu;
use skyblock\player\AetherPlayer;

class EffectsCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View your active potion effects");
	}


	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		(new PotionsMenu($player))->send($player);
	}
}