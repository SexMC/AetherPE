<?php

declare(strict_types=1);

namespace skyblock\items\armor\growth;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerKillPveEvent;
use skyblock\items\armor\ArmorSet;
use skyblock\items\armor\ArmorSetAbility;
use skyblock\player\AetherPlayer;

class GrowthSetAbility extends ArmorSetAbility {
	public function getDesiredEvents() : array{
		return [PlayerKillPveEvent::class];
	}

	public function tryCall(Event $event) : void{
		assert($event instanceof PlayerKillPveEvent);

		$p = $event->getPlayer();

		if(ArmorSet::getCache($p) instanceof GrowthSet){
			$this->onActivate($p, $event);
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setHealth($player->getPveData()->getHealth() + (0.01 * $player->getPveData()->getMaxHealth()));
	}
}