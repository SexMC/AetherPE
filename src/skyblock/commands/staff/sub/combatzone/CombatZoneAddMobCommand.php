<?php

declare(strict_types=1);

namespace skyblock\commands\staff\sub\combatzone;


use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use skyblock\commands\AetherSubCommand;
use skyblock\commands\arguments\PveEntityArgument;
use skyblock\Main;
use skyblock\misc\pve\zone\CombatZone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\player\AetherPlayer;

class CombatZoneAddMobCommand extends AetherSubCommand {


	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("zone"));
		$this->registerArgument(1, new PveEntityArgument("entity"));
		$this->registerArgument(2, new IntegerArgument("maxEntities"));
		$this->registerArgument(3, new IntegerArgument("spawnSpeed"));

	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		var_dump($args);
		$zone = ZoneHandler::getInstance()->getZone($args["zone"]);

		if(!$zone instanceof CombatZone){
			$player->sendMessage(Main::PREFIX . "Invalid zone ");
			return;
		}

		$zone->addMob($args["entity"], $args["maxEntities"], $args["spawnSpeed"]);
	}
}