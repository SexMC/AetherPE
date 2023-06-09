<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\world\particle\HugeExplodeParticle;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ability\AreaDamageAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;


class ExplosiveBow extends SkyBlockBow {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->properties->setDescription([
			"§r§6§lABILITY \"§r§5Explosive Shot§l§6\"",
			"§r§e\"Creates an explosion on impact.",
			"§r§eEvery monster caught in this explosion",
			"§r§etakes the full damage of the weapon.\"",
			"§r§b§lMANA COST \"§r§30§l§b\"",
		]);

		$this->properties->setRarity(Rarity::epic());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 100));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 20));
	}

	public function onProjectileHitEvent(AetherPlayer $player, ProjectileHitEvent $event) : void{


		$res = (new AreaDamageAbility(8, PveUtils::getItemDamage($this), false, "§5Explosive Shot", 30, 0))->start($player, $this);


		if($res){
			$player->getWorld()->addParticle($event->getRayTraceResult()->getHitVector(), new HugeExplodeParticle());
		}
	}
}