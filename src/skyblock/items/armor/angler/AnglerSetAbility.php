<?php

declare(strict_types=1);

namespace skyblock\items\armor\angler;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\skills\SkillLevelupEvent;
use skyblock\items\armor\ArmorSet;
use skyblock\items\armor\ArmorSetAbility;
use skyblock\player\AetherPlayer;

class AnglerSetAbility extends ArmorSetAbility {
	public function getDesiredEvents() : array{
		return [SkillLevelupEvent::class];
	}

	public function tryCall(Event $event) : void{
		assert($event instanceof SkillLevelupEvent);

		$p = $event->getPlayer();

		if(ArmorSet::getCache($p) instanceof AnglerSet){
			$this->onActivate($p, $event);
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		assert($player instanceof AetherPlayer);
		assert($event instanceof SkillLevelupEvent);
		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() - ($event->getOldLevel() * 10));
		$player->getPveData()->setMaxHealth($player->getPveData()->getMaxHealth() + ($event->getNewLevel() * 10));
	}
}