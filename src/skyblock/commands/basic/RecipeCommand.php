<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use skyblock\commands\AetherCommand;
use skyblock\menus\recipe\RecipesMenu;
use skyblock\player\AetherPlayer;

class RecipeCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Craft custom items");
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		(new RecipesMenu($player))->send($player);
	}
}