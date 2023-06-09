<?php

declare(strict_types=1);

namespace skyblock\items\pets\types;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\ProjectileHitEntityEvent;
use pocketmine\event\entity\ProjectileHitEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\itemattribute\ItemAttributeHolder;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\pets\Pet;
use skyblock\items\pets\PetInstance;
use skyblock\items\PvEItemEditor;
use skyblock\items\SkyblockItems;
use skyblock\items\weapons\PigmanSword;
use skyblock\misc\pve\PveDataRegenerator;
use skyblock\player\AetherPlayer;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\traits\StringIntCache;
use skyblock\utils\PveUtils;
use SOFe\AwaitGenerator\Await;

class SkeletonPet extends Pet {
	use AwaitStdTrait;

	use PlayerCooldownTrait;

	public function getCraftingIngredient() : Item{
		return SkyblockItems::ENCHANTED_BONE()->setCount(16);
	}

	public function getName() : string{
		return "Skeleton Pet";
	}

	public function getIdentifier() : string{
		return "skeleton";
	}

	public function applyStats(ItemAttributeHolder $item, int $level, int $rarity) : void{
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::CRITICAL_CHANCE(), 0.15 * $level));
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::CRITICAL_DAMAGE(), 0.3*$level));
	}

	public function getAbilityText(int $level, int $rarity) : array{
		$x = match($rarity) {
			self::RARITY_UNCOMMON => 0.1,
			self::RARITY_COMMON, self::RARITY_RARE => 0.15,
			default => 0.2
		};


		$arr = [
			"§r§6§l» §r§6Bone Arrow §l§6«",
			"§r§7Increase arrow damage by §a" . ($x * $level) . "%",
		];

		if($rarity >= self::RARITY_RARE){
			$x = match($rarity){
				self::RARITY_RARE => 0.12,
				self::RARITY_EPIC => 0.20,
				self::RARITY_LEGENDARY => 0.3
			};

			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Combo §l§6«";
			$arr[] = "§r§7Gain a combo stack for every";
			$arr[] = "§r§7bow hit granting +§a" . $x * 100;
			$arr[] = "§r§7" . PveUtils::getStrength() . "§7 stacks disappear";
			$arr[] = "§r§7after 8 seconds.";
		}

		if($rarity === self::RARITY_LEGENDARY){
			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Skeletal Defense §l§6«";
			$arr[] = "§r§7Your skeleton shoots an arrow";
			$arr[] = "§r§7dealing §a30x§7 your " . PveUtils::getCritDamage();
			$arr[] = "§r§7when a mob gets close to";
			$arr[] = "§r§7you. (5s cooldown)";
		}


		return $arr;
	}

	public function getDesiredEvents() : array{
		return [
			PlayerAttackPveEvent::class,
		];
	}


	public function getMaxLevel() : int{
		return 100;
	}

	public function onUse(Player $player, PetInstance $instance, Event $event) : void{
		assert($event instanceof PlayerAttackPveEvent);

		$x = match($instance->getRarity()) {
			self::RARITY_UNCOMMON => 0.1,
			self::RARITY_COMMON, self::RARITY_RARE => 0.15,
			default => 0.2
		};

		$event->multiplyDamage($x * $instance->getLevel() / 100, "skeleton-pet-bone-arrow");
	}

	public function tryCall(Event $event) : void{
		if($event instanceof PlayerAttackPveEvent){
			$p = $event->getPlayer();

			if($event->getProjectile() === null) return;

			if($p->getPetData()->getActivePetId() === $this->getIdentifier()){
				$pet = $p->getPetData()->getPetCollection()[$this->getIdentifier()];
				$this->onUse($p, $pet, $event);
			}
		}
	}

	public function onActivate(Player $player, PetInstance $pet) : bool{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setCritDamage($player->getPveData()->getCritDamage() + 0.3 * $pet->getLevel());
		$player->getPveData()->setCritChance($player->getPveData()->getCritChance() + 0.15 * $pet->getLevel());


		if($pet->getRarity() === self::RARITY_LEGENDARY){
			Await::f2c(function() use ($player, $pet){
				while($player->getPetData()->getActivePetId() === $this->getIdentifier() && $player->getPetData()->getPetCollection()[$this->getIdentifier()]->getRarity() === self::RARITY_LEGENDARY){
					yield $this->getStd()->sleep(20);

					if(!$this->isOnCooldown($player)){
						$e = $player->getWorld()->getNearestEntity($player->getPosition(), 4, PveEntity::class);
						if($e instanceof PveEntity){
							(new PlayerAttackPveEvent($player, $e, $player->getPveData()->getCritDamage() * 30))->call();
							$this->setCooldown($player, 5);
						}
					}
				}
			});
		}

		return true;
	}

	public function onDeActivate(Player $player, PetInstance $pet) : bool{
		assert($player instanceof AetherPlayer);

		$player->getPveData()->setCritDamage($player->getPveData()->getCritDamage() - 0.3 * $pet->getLevel());
		$player->getPveData()->setCritChance($player->getPveData()->getCritChance() - 0.15 * $pet->getLevel());

		return true;
	}

	public function getSkillType() : int{
		return self::TYPE_COMBAT;
	}

	public function getEntityId() : string{
		return EntityIds::SKELETON;
	}
}