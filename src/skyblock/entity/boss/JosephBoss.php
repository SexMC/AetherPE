<?php

declare(strict_types=1);

namespace skyblock\entity\boss;

use pocketmine\entity\effect\VanillaEffects;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\sound\AnvilFallSound;
use pocketmine\world\sound\AnvilUseSound;
use skyblock\caches\combat\CombatCache;
use skyblock\items\crates\types\AstronomicalCrate;
use skyblock\items\lootbox\types\JosephsRemainsLootbox;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\EntityUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Flowable;
use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\Human;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\EntityFlameParticle;
use skyblock\Main;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\PlayerCooldownTrait;


class JosephBoss extends Human {
	use PlayerCooldownTrait;
	use AwaitStdTrait;

	const ATTACK_COOLDOWN = 20;
	const SEARCH_COOLDOWN = 5;
	const KNOCKBACK_TICKS = 1;

	/** @var int */
	public int $attackCooldown = 0;
	/** @var int */
	public int $knockBackTicks = 0;

	public int $fireCD = 0;

	/** @var int */
	public float $damage = 20;
	/** @var float */
	public float $speed = 1.15;


	/** @var int */
	protected int $searchTargetCooldown = 0;
	/** @var null|Player */
	protected ?Entity $target = null;

	private array $damages = [];

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setCanSaveWithChunk(false);

		$this->setScale(3);
		$this->setMaxHealth(1000);
		$this->setHealth($this->getMaxHealth());

		$this->setNameTag($this->getNameTag());
		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);
	}

	public function getNameTag() : string{
		$format = number_format($this->getHealth(), 2);
		return "§r§l§cBOSS§r§c Joseph, Crowned King\n§r§c$format §l❤ ";
	}

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
			$entity = $this->getWorld()->getNearestEntity($this->getPosition(), 20, Player::class);
			if($entity !== null) {
				$this->target = $entity;
			}
			return parent::onUpdate($currentTick);
		}

		if($this->target === null) {
			return parent::onUpdate($currentTick);
		}

		if(!$this->target->isOnline()){
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

		if(mt_rand(1, 100) === 1){
			$this->fireresAbility();
		}

		if(++$this->fireCD >= 120){
			$this->fireCD = 0;

            foreach (EntityUtils::getNearbyEntitiesFromPosition($this->getPosition(), 25) as $e) {
				if(!$e instanceof Player) continue;
				if($this->isOnCooldown($e)) continue;

				$this->setCooldown($e, 15);
				$this->lazer($e);
			}
		}

		if(mt_rand(1, 200) === 1){
			$this->launch();
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

		if(mt_rand(1, 20) === 1 && $this->motion->y <= 0.1){
			$this->motion->y += 0.50;
		}


		$this->move($this->motion->x, $this->motion->y, $this->motion->z);
		$this->lookAt($this->target->getLocation());

		return parent::onUpdate($currentTick);
	}

	public function getLine(Location $from, Location $to, float $addition): array {

		$direction = $to->subtractVector($from);
		$locations = [];

		for($d = $addition; $d < $direction->length(); $d += $addition) {
			$locations[] = (clone $from)->addVector((clone $direction)->normalize()->multiply($d));
		}

		return $locations;
	}

	public function fireresAbility(): void {
        foreach (EntityUtils::getNearbyEntitiesFromPosition($this->getPosition(), 30) as $e) {
			if(!$e instanceof Player) continue;
			if(!$e->getEffects()->has(VanillaEffects::FIRE_RESISTANCE())) continue;

			$e->sendMessage(Main::PREFIX . "§r§l§cBOSS§r§c Joseph, Crowned King§f§l: §r§cYou think your enchantments can negate my type of fire? §r§c- Fire Resistance Effect");
			$e->getEffects()->remove(VanillaEffects::FIRE_RESISTANCE());
		}
	}

	public function launch(): void {
        foreach (EntityUtils::getNearbyEntitiesFromPosition($this->getPosition(), 30) as $e) {
			if(!$e instanceof Player) continue;

			$e->setMotion(new Vector3(0, 4, 0));
			$e->sendMessage(Main::PREFIX . "§r§l§cBOSS§r§c Joseph, Crowned King§f§l: §r§c* stomps the ground * you guys are getting ANNOYING");
			$e->getWorld()->addSound($e->location, new AnvilFallSound(), [$e]);
			$e->setHealth($e->getHealth() - 4);
		}
	}

	public function kill() : void{
		parent::kill();

		arsort($this->damages);

		$placing = 1;

		$msg = [];

		$msg[] = "§r§c§lBOSS§r§c Joseph, Crowned King §lhas been defeated";
		$msg[] = "§r§cTop 5 players receive better loot, everyone else receives the same thing";

		foreach($this->damages as $username => $damage) {
			$s = (new Session($username));

			if($placing >= 6){
				$s->addCollectItem(AstronomicalCrate::getInstance()->getKeyItem(3));
			} else {
				$s->addCollectItem(JosephsRemainsLootbox::getItem()->setCount($c = (6 - ($placing))));
				$msg[] = str_repeat("", 2) .  "§r§l§6* {$placing}ST PLACE";
				$msg[] = str_repeat("", mt_rand(1, 5)) .  "§r§l§c{$c}X §r§l§fLOOTBOX: §l§cJOSEPH'S REMAINS §r§l§c* §r§c{$username} §r§c($damage DMG)";
				$msg[] = "§r";
			}

			++$placing;
		}

		$msg[] = "§r§l§6* EVERYONE ELSE WHO ATTENDED:";
		$msg[] = "§r§l§c3X §r§l§f§r§l§c* §r§l§5G§da§5l§da§5x§dy §5K§de§5y§d: §5Astronomical §r§7(Use at Spawn) §r§c@everyone";
		$msg[] = "§r";

		Utils::announce(implode("\n§r", $msg));
	}

	public function lazer(Player $player): void {
		$player->sendMessage(Main::PREFIX . "§r§l§cBOSS§r§c Joseph, Crowned King§f§l: §r§cBurn children, BURN!");
		$player->setOnFire(60);

		foreach($this->getLine($this->location, $player->location, 0.75) as $vec){
			$this->getWorld()->addParticle($vec, new EntityFlameParticle());
		}

		Await::f2c(function() use ($player) {
			for($i = 0; $i <= 4; $i++){
				yield from $this->getStd()->sleep(1);

				if(!$player->isOnline()) break;


				$player->setHealth($player->getHealth() - 1);
				$player->getWorld()->addParticle($player->location, new BlockBreakParticle(VanillaBlocks::MAGMA()));
				$player->doHitAnimation();

				yield from $this->getStd()->sleep(19);
			}
		});
	}

	public function attack(EntityDamageEvent $source): void {
		$this->knockBackTicks = self::KNOCKBACK_TICKS;

		if($this->location->world->isLoaded()){
			parent::attack($source);
		}

		if($source instanceof EntityDamageByEntityEvent && $source->getFinalDamage() > 0){
			$p = $source->getDamager();

			if($p instanceof Player){
				if(!isset($this->damages[$p->getName()])){
					$this->damages[$p->getName()] = 0;
				}

				$this->damages[$p->getName()] += $source->getFinalDamage();
			}
		}
		
		$this->setNameTag($this->getNameTag());
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