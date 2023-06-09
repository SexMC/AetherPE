<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use pocketmine\item\ItemIdentifier;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;


class SavannaBow extends SkyBlockBow {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->properties->setDescription([
			"§r§7All damage dealth wit this bow",
			"§r§7is §adoubled§7.",
		]);

		$this->properties->setRarity(Rarity::uncommon());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 50));
	}

	public function onProjectileHitPveEvent(PlayerAttackPveEvent $event) : void{
		parent::onProjectileHitPveEvent($event);
		$event->multiplyDamage(2, "savannabow");
	}
}