<?php

declare(strict_types=1);

namespace skyblock\commands\staff\sub\combatzone;

use CortexPE\Commando\args\RawStringArgument;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\pve\zone\CombatZone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\player\AetherPlayer;

class CombatZoneRemoveMobSubcommand extends AetherSubCommand {


	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("zone"));
		$this->registerArgument(1, new RawStringArgument("mob"));
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		$zone = ZoneHandler::getInstance()->getZone($args["zone"]);

		if(!$zone instanceof CombatZone){
			$player->sendMessage(Main::PREFIX . "Invalid zone ");
			return;
		}


		$zone->removeMob($args["mob"]);
		$player->sendMessage(Main::PREFIX . "Removed §c" . $args["mob"] . " §7from the zone");
	}
}