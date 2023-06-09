<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\entity\boss\PveEntity;
use skyblock\events\player\EntityHitBowEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class Punch extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Arrows will deal more knockback");
		$this->setApplicableTo(self::ITEM_BOW);
		$this->setMaxLevel(2);
		$this->setEvents([EntityHitBowEvent::class]);

		return new CustomEnchantIdentifier("punch", "Punch");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof EntityHitBowEvent) return;

		$s = $event->getSource();

		var_dump("called");
		if($s instanceof EntityDamageByEntityEvent){
			var_dump("sets");
			$s->setKnockBack($s->getKnockBack() * ($enchantInstance->getLevel() + 1));
		}
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof EntityHitBowEvent) return false;

		var_dump("here");
		return $event->getEntity() instanceof PveEntity;
	}
}