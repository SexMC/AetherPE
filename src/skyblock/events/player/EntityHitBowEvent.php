<?php

declare(strict_types=1);

namespace skyblock\events\player;

use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Arrow;
use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\Event;
use pocketmine\item\Bow;
use pocketmine\player\Player;

class EntityHitBowEvent extends Event {

	public function __construct(
		private Player $damager,
		private Entity $entity,
		private Arrow $arrow,
		private EntityDamageEvent $source
	){
		//PROJECTILE CLASS LINE 300 child dmg
	}

	/**
	 * @return Player
	 */
	public function getDamager() : Player{
		return $this->damager;
	}

	/**
	 * @return Entity
	 */
	public function getEntity() : Entity{
		return $this->entity;
	}
	/**
	 * @return Arrow
	 */
	public function getArrow() : Arrow{
		return $this->arrow;
	}

	/**
	 * @return EntityDamageEvent
	 */
	public function getSource() : EntityDamageEvent{
		return $this->source;
	}
}