<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\world\sound\ExplodeSound;
use skyblock\entity\projectile\DreadlordSkullEntity;
use skyblock\items\ability\AreaDamageAbility;
use skyblock\items\ability\ShootProjectileAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;


class DreadlordSword extends SkyBlockWeapon implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("dreadlord_sword", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));


		$this->properties->setDescription([
			"§r§7I'm the cause of all deaths,",
			"§r§7i created it.",
			"§r",
			"§r§4§lABILITY \"§r§cDread Lord§l§4\"",
			"§r§c\"Shoot a skull that deals 500",
			"§r§cdamage\"",
			"§r§b§lMANA COST \"§r§340§l§b\"",
		]);

		$this->properties->setRarity(Rarity::rare());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 50));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 50));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::INTELLIGENCE(), 85));
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{

		(new ShootProjectileAbility(DreadlordSkullEntity::class, "§cDread Lord", 40, 1))->start($player, $this);

		return parent::onClickAir($player, $directionVector);
	}

	public function onProjectileHitEvent(AetherPlayer $player, ProjectileHitEvent $event) : void{
		parent::onProjectileHitEvent($player, $event);

		$pos = $event->getEntity()->getPosition();

		$pos->getWorld()->addSound($pos, new ExplodeSound());
		$pos->getWorld()->addParticle($pos, new HugeExplodeParticle());

		(new AreaDamageAbility(5, 500, false, "dread_lord_no_cd", 0, 0))->start($player, $this);
	}
}