<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\Event;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class BaneOfArthropods extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Deal 10%*(ce level) more damage to spiders, cave spiders & silverfish");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(5);
		$this->setEvents([PlayerAttackPveEvent::class]);

		return new CustomEnchantIdentifier("bane_of_arthropods", "Bane Of Arthropods");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerAttackPveEvent) return;

		$event->multiplyDamage(0.10 * $enchantInstance->getLevel(), "bane of arthropods +" . (10 * $enchantInstance->getLevel()));
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerAttackPveEvent) return false;

		$e = $event->getEntity();

		return in_array($e->getNetworkID(), [EntityIds::SPIDER, EntityIds::CAVE_SPIDER, EntityIds::SILVERFISH]);
	}
}