<?php

declare(strict_types=1);

namespace skyblock\misc\pve;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use skyblock\player\AetherPlayer;

class PveDataRegenerator extends Task {
	public function onRun() : void{
		/** @var AetherPlayer $player */
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			self::regenerateHealth($player);
			self::regenerateIntelligence($player);
		}
	}

	public static function regenerateHealth(AetherPlayer $player): void {
		$gainedHealth = (($player->getPveData()->getMaxHealth() * 0.01) + 1.5) * 1; //Health=((MaxHealth*0.01)+1.5)*Multiplier
		$player->getPveData()->setHealth($player->getPveData()->getHealth() + $gainedHealth);
	}

	public static function regenerateIntelligence(AetherPlayer $player): void {
		$regenIntelligence = $player->getPveData()->getMaxIntelligence() * 0.04;
		$player->getPveData()->setIntelligence($player->getPveData()->getIntelligence() + $regenIntelligence);
	}
}