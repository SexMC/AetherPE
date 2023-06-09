<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\player\PlayerBaitConsumeEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class Caster extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("§a5%*(ce level) §7chance to not consume bait.");
		$this->setApplicableTo(self::ITEM_FISHING_ROD);
		$this->setMaxLevel(4);
		$this->setEvents([PlayerBaitConsumeEvent::class]);

		return new CustomEnchantIdentifier("caster", "Caster", true);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerBaitConsumeEvent) return;

		$event->setShouldConsume(false);
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerBaitConsumeEvent) return false;

		return mt_rand(1, 100) <= $enchantInstance->getLevel() * 5;
	}
}