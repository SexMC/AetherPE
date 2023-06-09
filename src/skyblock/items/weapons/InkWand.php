<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use pocketmine\block\utils\DyeColor;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\DustParticle;
use pocketmine\world\sound\ExplodeSound;
use skyblock\entity\projectile\InkBombEntity;
use skyblock\items\ability\AreaDamageAbility;
use skyblock\items\ability\ShootProjectileAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;


class InkWand extends SkyBlockWeapon {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->properties->setDescription([
			"§r§7The purpose of literature is",
			"§r§7to turn blood into ink.",
			"§r",
			"§r§4§lABILITY \"§r§3Ink Bomb§l§4\"",
			"§r§c\"Shoot an ink bomb in front of",
			"§r§cyou dealing §a10.000§r§c base damage",
			"§r§cand giving blindness\"",
			"§r§b§lMANA COST \"§r§360§l§b\"",
			"§r§8Cooldown: §a30s",
		]);

		$this->properties->setRarity(Rarity::epic());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 130));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 90));
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{

		(new ShootProjectileAbility(InkBombEntity::class, "§3Ink Bomb", 60, 30))->start($player, $this);

		return parent::onClickAir($player, $directionVector);
	}

	public function onProjectileHitEvent(AetherPlayer $player, ProjectileHitEvent $event) : void{
		parent::onProjectileHitEvent($player, $event);

		$pos = $event->getEntity()->getPosition();
		$pos->getWorld()->addSound($pos, new ExplodeSound());

		$bb = $event->getEntity()->getBoundingBox()->expandedCopy(8, 8, 8);
		for($i = 0; $i <= 20; $i++){
			$x = mt_rand((int) $bb->minX, (int) $bb->maxX);
			$z = mt_rand((int) $bb->minZ, (int) $bb->maxZ);
			$y = mt_rand((int) $pos->y, (int) $bb->maxY);


			$pos->getWorld()->addParticle(new Vector3($x, $y, $z), new DustParticle(DyeColor::LIGHT_BLUE()->getRgbValue()));
		}

		(new AreaDamageAbility($this->range, 10000, true, "ink_bomb_no_cd", 0, 0))->start($player, $this);
	}
}