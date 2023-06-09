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
use skyblock\utils\EntityUtils;

class Thunderlord extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Chance to strike a monster with lightning that deals 8%*(ce level) more damage");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(4);
		$this->setEvents([PlayerAttackPveEvent::class]);

		return new CustomEnchantIdentifier("thunderlord", "Thunderlord");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerAttackPveEvent) return;

		$event->multiplyDamage(0.08 * $enchantInstance->getLevel(), "thunderlord +" . (10 * $enchantInstance->getLevel()));
		EntityUtils::spawnLightning($event->getEntity()->getLocation());
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerAttackPveEvent) return false;

		return mt_rand(1, 100) <= $enchantInstance->getLevel() * 6;
	}
}