<?php

declare(strict_types=1);

namespace skyblock\entity\boss;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\Entity;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\particle\HugeExplodeSeedParticle;
use pocketmine\world\sound\ExplodeSound;
use skyblock\entity\ai\MovingEntity;
use skyblock\entity\projectile\Fireball;
use skyblock\events\CustomEntityDamageByEntityEvent;
use skyblock\items\crates\types\AetherCrate;
use skyblock\items\crates\types\CommonCrate;
use skyblock\items\crates\types\HeroicCrate;
use skyblock\items\crates\types\LegendaryCrate;
use skyblock\items\special\types\EssencePouch;
use skyblock\items\special\types\XPBottleItem;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\utils\CustomEnchantUtils;
use skyblock\utils\EntityUtils;
use skyblock\utils\Utils;

class WitheredBlazeBoss extends MovingEntity  {
	use PlayerCooldownTrait;

	private int $ticks = 0;

	protected int $distance = 20;

	public float $damage = 8;


	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, 1);

		$this->setMaxHealth(300);
		$this->setHealth($this->getMaxHealth());
		$this->setNameTagAlwaysVisible(true);
	}

	protected function getInitialSizeInfo() : EntitySizeInfo{
		return new EntitySizeInfo(1.8, 0.5);
	}

	public static function getNetworkTypeId() : string{
		return EntityIds::BLAZE;
	}

	public function getName() : string{
		return "§r§l§8With§7ered §6Bla§eze";
	}

	public function getNameTag() : string{
		$format = number_format($this->getHealth(), 2);
		return "§r§l§8With§7ered §6Bla§eze\n§r§c{$format} §l❤";
	}

	public function kill() : void{
		parent::kill();

		$cause = $this->getLastDamageCause();

		if($cause instanceof EntityDamageByEntityEvent){
			$damager = $cause->getDamager();

			if($damager instanceof Player && $damager->isOnline()){
				$key = [LegendaryCrate::getInstance()->getKeyItem(1), AetherCrate::getInstance()->getKeyItem(1), HeroicCrate::getInstance()->getKeyItem(1), CommonCrate::getInstance()->getKeyItem(1)];
				$rand = $key[array_rand($key)];
				$items = [$rand, XPBottleItem::getItem(mt_rand(1000, 50000), "WITHERED BLAZE BOSS"), EssencePouch::getItem(100, 1000)];
				foreach($items as $i){
					Utils::addItem($damager, $i);
				}
			}
		}
	}

	public function attack(EntityDamageEvent $source) : void{
		parent::attack($source);

		if($source instanceof CustomEntityDamageByEntityEvent){

			if(mt_rand(1, 3) === 1){
				$damager = $source->getDamager();
				if($damager instanceof Player){
					if(!$this->isOnCooldown($damager)){
						foreach($source->getDamagerCustomEnchantsReactionManager()->getActivating() as $reflect){
							$source->reflectFromDamagerToEntity($reflect);
							$this->setCooldown($damager, 8);
							$damager->getWorld()->addParticle($damager->getPosition(), new HugeExplodeSeedParticle());
							$damager->getWorld()->addSound($damager->getPosition(), new ExplodeSound());
							$txt = $reflect->getCustomEnchant()->getIdentifier()->getName()  . " " . CustomEnchantUtils::roman($reflect->getLevel());
							$damager->sendMessage("§r§l§8With§7ered §6Bla§eze§f: §r§6$txt §r§7belongs to you i believe.");
							break;
						}
					}
				}
			}
		}

		$this->setNameTag($this->getNameTag());
	}

	public function attackEntity(Entity $entity) : void{
		parent::attackEntity($entity);
		$this->searchNearbyTarget();
	}

	public function onUpdate(int $currentTick) : bool{
		$parent = parent::onUpdate($currentTick);

		if(++$this->ticks >= 50){
			$this->ticks = 0;
			$this->setHealth($this->getHealth() + 2);

			$this->getWorld()->addParticle($this->getPosition(), new BlockBreakParticle(VanillaBlocks::BEDROCK()));
		}

		/*if($this->target instanceof Player) {
			if(mt_rand(1, 100) === 1 && !$this->isOnCooldown($this->target)){
				$this->setCooldown($this->target, 10);
				$this->shoot();

				$this->target->sendMessage(Main::PREFIX . "§r§l§8With§7ered §6Bla§eze§f: §r§7Feel it, feel the pain little one.");
			}
		}*/

		return $parent;
	}

	public function shoot(): void {
		$location = $this->getLocation();

		(new Fireball(Location::fromObject($this->getEyePos(), $this->getWorld(), ($location->yaw > 180 ? 360 : 0) - $location->yaw, -$location->pitch), $this))->spawnToAll();
	}


	public function searchNearbyTarget(): void {
        foreach (EntityUtils::getNearbyEntitiesFromPosition($this->getPosition(), 5) as $e) {
			if($e instanceof Player){
				if($this->target instanceof Player){
					if($e->getId() !== $this->target->getId()){
						$this->target = $e;
						break;
					}
				}
			}
		}
	}

	public function doesJump() : bool{
		return true;
	}

	public function setHealth(float $amount) : void{
		parent::setHealth($amount);
		$this->networkPropertiesDirty = true;
	}
}