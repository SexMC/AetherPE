<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\commands\staff\sub\combatzone\CombatZoneAddMobCommand;
use skyblock\commands\staff\sub\combatzone\CombatZoneCreateSubCommand;
use skyblock\commands\staff\sub\combatzone\CombatZoneDeleteSubCommand;
use skyblock\commands\staff\sub\combatzone\CombatZoneInfoSubCommand;
use skyblock\commands\staff\sub\combatzone\CombatZoneRemoveMobSubcommand;

class CombatZoneCommand extends AetherCommand{

	protected function prepare() : void{
		$this->setPermission("skyblock.commands.combatzone");

		$this->registerSubCommand(new CombatZoneCreateSubCommand("create"));
		$this->registerSubCommand(new CombatZoneAddMobCommand("addmob"));
		$this->registerSubCommand(new CombatZoneInfoSubCommand("info"));
		$this->registerSubCommand(new CombatZoneRemoveMobSubcommand("removemob"));

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{}
}