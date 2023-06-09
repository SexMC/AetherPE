<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\menus\skills\SkillsBrowseMenu;
use skyblock\menus\skills\SkillsMenu;
use skyblock\misc\skills\CombatSkill;
use skyblock\misc\skills\MiningSkill;
use skyblock\player\AetherPlayer;

class SkillsCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View skills");
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		(new SkillsBrowseMenu($player))->send($player);
	}
}