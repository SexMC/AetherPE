<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\world\particle\EndermanTeleportParticle;
use pocketmine\world\sound\EndermanTeleportSound;
use skyblock\items\ability\AreaDamageAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;
use skyblock\traits\StaticPlayerCooldownTrait;
use skyblock\utils\PveUtils;

class EnderBow extends SkyBlockBow {
	use StaticPlayerCooldownTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->properties->setDescription([
			"§r§7§oLike a shimmering beacon in the dark, an Ender Bow",
			"§r§7§oin the hands of a skilled adventurer is a ticket",
			"§r§7§oto new adventures and uncharted territories in the vast",
			"§r§7§othe vast and mysterious world of AetherPE.",
			"§r",
			"§r§4§lABILITY \"§r§3Ender Warp§l§4\"",
			"§r§c\"Shoots an ender pearl upon landing",
			"§r§cyou deal damage to all Monsters",
			"§r§cin §a8.0§c block radius for",
			"§r§c§a100 §bbase magic damage",
			"§r§b§lMANA COST \"§r§350§l§b\"",
			"§r§8Cooldown: §a45s",
		]);

		$this->properties->setRarity(Rarity::rare());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 60));
	}



	public function onProjectileHitEvent(AetherPlayer $player, ProjectileHitEvent $event) : void{
		parent::onProjectileHitEvent($player, $event);

		$pos = $event->getEntity()->getPosition();

		$e = (new AreaDamageAbility(8, PveUtils::getFinalMagicDamage(100, $player->getPveData()), true, "Ender Warp", 50, 45));
		$bool = $e->start($player, $this);

		if($bool){
			$player->teleport($pos);
			$player->getWorld()->addSound($pos, new EndermanTeleportSound());
			$player->getWorld()->addParticle($pos, new EndermanTeleportParticle());
		}

	}
}