<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\player\EntityHitBowEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class Power extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Increases bow damage by 8%*(CE level)");
		$this->setApplicableTo(self::ITEM_BOW);
		$this->setMaxLevel(5);
		$this->setEvents([EntityHitBowEvent::class]);

		return new CustomEnchantIdentifier("power", "Power", false);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof EntityHitBowEvent) return;

		$event->getSource()->setBaseDamage($event->getSource()->getBaseDamage() + ($event->getSource()->getBaseDamage() * (8 * $enchantInstance->getLevel())));
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof EntityHitBowEvent) return false;
		if($player->getId() !== $event->getDamager()->getId()) return false;

		return true;
	}
}