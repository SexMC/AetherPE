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

class Execute extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Deal 0.2%*(ce level) more damage for each percent of health your enemy is missing");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(4);
		$this->setEvents([PlayerAttackPveEvent::class]);

		return new CustomEnchantIdentifier("execute", "Execute");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerAttackPveEvent) return;
		$e = $event->getEntity();

		$missingHealth = $e->getMaxHealth() - $e->getHealth();
		if($missingHealth <= 0) return;

		$missingPercent = $missingHealth / $e->getMaxHealth() * 100;


		$event->multiplyDamage((0.02 * $enchantInstance->getLevel()) * $missingPercent, "cubism +" . (10 * $enchantInstance->getLevel()));
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerAttackPveEvent) return false;

		return true;
	}
}