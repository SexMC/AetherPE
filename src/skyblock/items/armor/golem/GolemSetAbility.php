<?php

declare(strict_types=1);

namespace skyblock\items\armor\golem;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerKillPveEvent;
use skyblock\items\armor\ArmorSet;
use skyblock\items\armor\ArmorSetAbility;

class GolemSetAbility extends ArmorSetAbility {
	public function getDesiredEvents() : array{
		return [PlayerKillPveEvent::class];
	}

	public function tryCall(Event $event) : void{
		assert($event instanceof PlayerKillPveEvent);

		$p = $event->getPlayer();

		if(ArmorSet::getCache($p) instanceof GolemSet){
			$this->onActivate($p, $event);
		}
	}

	public function onActivate(Player $player, Event $event) : void{
		$player->getEffects()->add(new EffectInstance(VanillaEffects::ABSORPTION(), 20 * 20, 2));
	}
}