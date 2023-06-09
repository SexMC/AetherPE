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

class Magnet extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Grants 1*(ce level) more experience orbs when fishing");
		$this->setApplicableTo(self::ITEM_FISHING_ROD);
		$this->setMaxLevel(4);
		$this->setEvents([PlayerPreFishEvent::class]);

		return new CustomEnchantIdentifier("magnet", "Magnet", false);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerPreFishEvent) return;

		$player->getXpManager()->addXp($enchantInstance->getLevel());
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerPreFishEvent) return false;

		return true;
	}
}