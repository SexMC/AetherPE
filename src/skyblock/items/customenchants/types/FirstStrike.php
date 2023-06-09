<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class FirstStrike extends BaseReactiveEnchant {
	private array $data = [];

	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Deal 25% * (ce level) more damage for the first hit on a mob");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(3);
		$this->setEvents([PlayerAttackPveEvent::class]);

		return new CustomEnchantIdentifier("first_strike", "First Strike", true);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerAttackPveEvent) return;


		if(!isset($this->data[$player->getName()])){
			$this->data[$player->getName()] = [];
		}

		$this->data[$player->getName()][] = $event->getEntity()->getId();
		$event->multiplyDamage(0.25 * $enchantInstance->getLevel(), "first strike");
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerAttackPveEvent) return false;


		return in_array($event->getEntity()->getId(), $this->data[$player->getName()] ?? []) === false;
	}
}