<?php

declare(strict_types=1);

namespace skyblock\commands\staff\sub\zone;

use skyblock\commands\AetherSubCommand;
use skyblock\misc\pve\Zone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\player\AetherPlayer;

class ZoneListSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setPermission("skyblock.commands.zone");
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		/** @var Zone $zone */
		foreach(ZoneHandler::getInstance()->getAllZones() as $zone){
			$player->sendMessage("§7Zone: §c" . $zone->getName() . " §7Type: §c" . $zone->getType());
		}
	}
}