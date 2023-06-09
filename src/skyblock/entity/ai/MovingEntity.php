<?php

declare(strict_types=1);

namespace skyblock\entity\ai;

use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Flowable;
use pocketmine\block\Netherrack;
use pocketmine\block\SoulSand;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Human;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\caches\combat\CombatCache;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

abstract class MovingEntity extends Living {
	use AwaitStdTrait;

	const ATTACK_COOLDOWN = 20;
	const SEARCH_COOLDOWN = 5;
	const KNOCKBACK_TICKS = 6;

	/** @var int */
	public int $attackCooldown = 0;
	/** @var int */
	public int $knockBackTicks = 0;

	/** @var int */
	public float $damage = 1;
	/** @var float */
	public float $speed = 1.15;

	protected int $distance = 150;

	protected bool $islandTargetting = false;

	protected ?Island $targetIsland = null;

	protected int $timer = 15;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);

		if($this->islandTargetting === true){
			Await::f2c(function() {
				while($this->isAlive()){
					if($this->targetIsland !== null){
						if(--$this->timer <= 0){
							$this->targetIsland = null;
							$this->timer = 15;
						}
					}

					$this->onIslandTick();
					yield $this->getStd()->sleep(20);
				}
			});
		}
	}


	/** @var int */
	protected int $searchTargetCooldown = 0;
	/** @var null|Player */
	protected ?Entity $target = null;

	public function onIslandTick(): void {}

	public function attackEntity(Entity $entity): void {
		$this->attackCooldown = self::ATTACK_COOLDOWN;
		$entity->attack(new EntityDamageByEntityEvent($this, $entity, EntityDamageByEntityEvent::CAUSE_ENTITY_ATTACK, $this->damage));
		$this->doArmSwingAnimation();

		if($entity instanceof AetherPlayer){
			CombatCache::getInstance()->setInCombat($entity, 8);
		}
	}

	public function onUpdate(int $currentTick): bool {
		$this->attackCooldown--;
		$this->searchTargetCooldown--;
		$this->knockBackTicks--;

		if(($this->target === null || $this->target->getPosition()->distance($this->getPosition()) >= 15) && $this->searchTargetCooldown < 1) {
			$this->searchTargetCooldown = self::SEARCH_COOLDOWN;
			$entity = $this->getWorld()->getNearestEntity($this->getPosition(), $this->distance, Player::class);
			if($entity !== null && $entity->isAlive()) {
				$this->target = $entity;
			}
			return parent::onUpdate($currentTick);
		}

		if($this->target === null) {
			return parent::onUpdate($currentTick);
		}

		if(!$this->target->isOnline() || !$this->target->isAlive()){
			$this->target = null;
			return parent::onUpdate($currentTick);
		}

		if($this->target->getPosition()->distance($this->getPosition()) > $this->distance){
			$this->target = null;
			return parent::onUpdate($currentTick);
		}

		if($this->isInReach($this->target) && $this->isAlive()) {
			$this->attackEntity($this->target);
		}

		if($this->target->isSurvival() && $this->target->getAllowFlight()){
			$this->target->setAllowFlight(false);
			$this->target->setFlying(false);
		}

		if($this->knockBackTicks > 0) {
			return parent::onUpdate($currentTick);
		}

		$x = $this->target->getPosition()->x - $this->getPosition()->x;
		$z = $this->target->getPosition()->z - $this->getPosition()->z;
		if ($x ** 2 + $z ** 2 < 0.7) {
			$this->motion->x = 0;
			$this->motion->z = 0;
		} else {
			$diff = abs($x) + abs($z);
			$this->motion->x = $this->speed * 0.15 * ($x / $diff);
			$this->motion->z = $this->speed * 0.15 * ($z / $diff);
		}

		if($this->doesJump() && $this->canJump()){
			$this->motion->y += 0.25;
		} else $this->motion->y += mt_rand(10, 100) / 1000;


		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		$this->lookAt($this->target->getLocation());

		return parent::onUpdate($currentTick);
	}



	public function attack(EntityDamageEvent $source): void {
		$this->knockBackTicks = self::KNOCKBACK_TICKS;

		if($this->location->world->isLoaded()){
			if(!$this->islandTargetting){
				parent::attack($source);
				return;
			}


			if($source->isCancelled()) return;

			if($source instanceof EntityDamageByEntityEvent){
				$damager = $source->getDamager();
				if($damager instanceof Player){
					if($this->targetIsland !== null){
						if(!$this->targetIsland->isMemberOrLeader($damager)){
							$source->cancel();
							$damager->sendMessage(Main::PREFIX . "Your island does not currently target this boss");
						}
					} else {
						$this->targetIsland = (new Session($damager))->getIslandOrNull();
					}
				}
			}

			if($source->isCancelled()) return;

			parent::attack($source);

			if(!$source->isCancelled()){
				$this->onNewHit();
			}
		}
	}

	protected function onNewHit(): void {
		$this->timer = 15;
	}


	private function canJump(): bool
	{
		$block = $this->getWorld()->getBlock($this->getPosition()->addVector($this->getDirectionVector()));
		if($block->getId() !== BlockLegacyIds::AIR && !$block instanceof Flowable && $block->getPosition()->getY() >= $this->getPosition()->getY()){
			return true;
		}

		return false;
	}

	public function isInReach(Entity $entity): bool {
		return $entity->getPosition()->distance($this->getPosition()) <= 2;
	}

	public function doesJump(): bool {
		return false;
	}

	public function doArmSwingAnimation(): void {
		$pk = new ActorEventPacket();
		$pk->eventId = ActorEvent::ARM_SWING;
		$pk->actorRuntimeId = $this->getId();
		$this->getWorld()->broadcastPacketToViewers($this->getPosition(), $pk);
	}
}