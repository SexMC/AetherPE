<?php


declare(strict_types=1);

namespace skyblock\misc\pve\ability;

use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\entity\projectile\Arrow as ArrowEntity;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\player\Player;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\traits\StringCooldownTrait;

class SkeletonBowShootAbility extends MobAbility{
	use StringCooldownTrait;

	public function shootBow(PveEntity $entity, Entity $target): void {
		$pos = $target->getPosition()->add(0, 1, 0);
		$entity->lookAt($pos);
		$direction = $entity->getDirectionVector();

		$location = $entity->getLocation();
		$entity = new ArrowEntity(Location::fromObject(
			$entity->getEyePos(),
			$entity->getWorld(),
			($location->yaw > 180 ? 360 : 0) - $location->yaw,
			-$location->pitch
		), $entity, true);
		$entity->setMotion($direction);
		$entity->setMotion($entity->getMotion()->multiply(2));
		$entity->spawnToAll();
	}

	public function attack(Player $player, PveEntity $entity, float $baseDamage) : bool {
		$this->shootBow($entity, $player);



		return false;
	}

	public static function getId() : string{
		return "skeleton-shoot-bow";
	}

	public function onTick(PveEntity $entity, int $tick) : void{
		if($entity->getTarget() !== null){
			if(!$this->isOnCooldown((string) $entity->getId())){
				$this->setCooldown((string) $entity->getId(), 2);

				$this->shootBow($entity, $entity->getTarget());
			}
		}
	}

	public function onDeath(PveEntity $entity, EntityDeathEvent $event) : void{
		$this->removeCooldown((string) $entity->getId());
	}

	public function onDamage(PveEntity $entity, PlayerAttackPveEvent $event) : void{}
}