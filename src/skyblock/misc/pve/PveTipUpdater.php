<?php

declare(strict_types=1);

namespace skyblock\misc\pve;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;


class PveTipUpdater extends Task{
	public function onRun() : void{
		/** @var AetherPlayer $player */
		foreach(Server::getInstance()->getOnlinePlayers() as $player){
			self::updateTip($player);
		}
	}

	public static function updateTip(AetherPlayer $player, string $extra = ""): void {
		$data = $player->getPveData();
		$maxHealth = number_format($data->getMaxHealth());
		$health = number_format($data->getHealth());
		$maxIntelligence = number_format($data->getMaxIntelligence());
		$intelligence = number_format($data->getIntelligence());
		$defense = number_format($data->getDefense());

		$tip = "§c{$health}/{$maxHealth}" . PveUtils::getHealthSymbol() . "   §a{$defense}" . PveUtils::getDefenseSymbol() . "   §b{$intelligence}/{$maxIntelligence}" . PveUtils::getIntelligenceSymbol();

		if($extra !== ""){
			$tip .= "\n" . $extra;
		}

		$player->sendTip($tip);
	}
}