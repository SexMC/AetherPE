<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class Knockback extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Deal more knockback to mobs");
		$this->setApplicableTo(self::ITEM_SWORD);
		$this->setMaxLevel(2);
		$this->setEvents([PlayerAttackPveEvent::class]);

		return new CustomEnchantIdentifier("knockback", "Knockback");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerAttackPveEvent) return;

		$victim = $event->getEntity();
		$attacker = $player;
		$diff = $victim->getPosition()->subtractVector($attacker->getPosition());
		$victim->knockBack($diff->x, $diff->z, $enchantInstance->getLevel() * 0.5);
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerAttackPveEvent) return false;

		return $event->getEntity() instanceof PveEntity;
	}
}