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

class Cleave extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Deals §a5%*(ce level)§7 of your damage dealt to other monsters within §a5 §7blocks of the target.");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(4);
		$this->setEvents([PlayerAttackPveEvent::class]);

		return new CustomEnchantIdentifier("cleave", "Cleave");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerAttackPveEvent) return;

		$damage = $event->getFinalDamage();
		foreach($event->getEntity()->getWorld()->getNearbyEntities($event->getEntity()->getBoundingBox()->expandedCopy(5, 5, 5)) as $e){
			if($e instanceof PveEntity && $e->getId() !== $event->getEntity()->getId()){
				$e = new PlayerAttackPveEvent($player, $e, 0.20 * $damage);
				$e->setData(["cleave" => true]);
				$e->call();
			}
		}
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerAttackPveEvent) return false;

		if(isset($event->getData()["cleave"])) return false; //so it doesn't create a chain of reactions

		return true;
	}
}