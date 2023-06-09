<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\player\PlayerPreFishEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class Lure extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Shortens the fishing time by 5% per level");
		$this->setApplicableTo(self::ITEM_FISHING_ROD);
		$this->setMaxLevel(6);
		$this->setEvents([PlayerPreFishEvent::class]);

		return new CustomEnchantIdentifier("lure", "Lure");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerPreFishEvent) return;

		$event->setOriginalFishTime((int) ceil($event->getOriginalFishTime() * (1 - (0.05 * $enchantInstance->getLevel()))));
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerPreFishEvent) return false;

		return true;
	}
}