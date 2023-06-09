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

class Cubism extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Deal 10%*(ce level) more damage to magma cubes, slimes and creepers");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(5);
		$this->setEvents([PlayerAttackPveEvent::class]);

		return new CustomEnchantIdentifier("cubism", "Cubism");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerAttackPveEvent) return;
		
		$event->multiplyDamage(0.10 * $enchantInstance->getLevel(), "cubism +" . (10 * $enchantInstance->getLevel()));
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerAttackPveEvent) return false;

		$e = $event->getEntity();

		return in_array($e->getNetworkID(), [EntityIds::MAGMA_CUBE, EntityIds::CREEPER, EntityIds::SLIME]);
	}
}