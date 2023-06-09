<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerKillPveEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;
use skyblock\sessions\Session;

class Scavenger extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Scavenge §6$0.4*level§r per monster level on kill (amount is rounded up)");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(4);
		$this->setEvents([PlayerKillPveEvent::class]);

		return new CustomEnchantIdentifier("scavenger", "Scavenger", false);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerKillPveEvent) return;
		$e = $event->getEntity();

		$session = new Session($player);
		$session->increasePurse((int) ($e->level * 0.3));
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerKillPveEvent) return false;

		return true;
	}
}