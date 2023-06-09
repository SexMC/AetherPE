<?php

declare(strict_types=1);

namespace skyblock\commands\staff\sub\zone;

use CortexPE\Commando\args\RawStringArgument;
use skyblock\commands\AetherSubCommand;
use skyblock\Main;
use skyblock\misc\pve\zone\CombatZone;
use skyblock\misc\pve\zone\Zone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\player\AetherPlayer;

class ZoneInfoCommand extends AetherSubCommand {


	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("zone"));
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		$zone = ZoneHandler::getInstance()->getZone($args["zone"]);

		if(!$zone instanceof Zone){
			$player->sendMessage(Main::PREFIX . "Invalid zone ");
			return;
		}


		$array = [
			"§7Name: §c" . $zone->getName(),
			"§7AABB: §c" . (string) $zone->getBb(),
		];

		$player->sendMessage(implode("\n", $array));
	}
}