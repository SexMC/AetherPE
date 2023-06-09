<?php

declare(strict_types=1);

namespace skyblock\items\pets\types;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\items\itemattribute\ItemAttributeHolder;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\pets\Pet;
use skyblock\items\pets\PetInstance;
use skyblock\items\SkyblockItems;
use skyblock\player\AetherPlayer;

class ChickenPet extends Pet {

	public function getCraftingIngredient() : Item{
		return SkyblockItems::ENCHANTED_CHICKEN()->setCount(8);
	}

	public function getName() : string{
		return "Chicken Pet";
	}

	public function getIdentifier() : string{
		return "chicken";
	}

	public function applyStats(ItemAttributeHolder $item, int $level, int $rarity) : void{
		$item->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), 2*$level));
	}

	public function getAbilityText(int $level, int $rarity) : array{
		$arr = [
            "§r§6§l» §r§6Light Feet §l§6«",
			"§r§7Reduces fall damage by §a" . ($this->getFallDamageReduction($rarity) * $level) . "%"
		];

		if($rarity >= self::RARITY_RARE){
			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Eggs-tra §l§6«";
			$arr[] = "§r§7Killing chickens has a §a" . $this->getEggDropChance($rarity) . "%";
			$arr[] = "§r§7chance to drop an egg";
		}

		if($rarity === self::RARITY_LEGENDARY){
			$arr[] = "§r";
			$arr[] = "§r§6§l» §r§6Mighty Chickens §l§6«";
			$arr[] = "§r§7Chicken minions work " . (0.3 * $level) . "%";
			$arr[] = "§r§7faster while on your island.";
		}


		return $arr;
	}

	public function getDesiredEvents() : array{
		return [EntityDeathEvent::class, EntityDamageEvent::class];
	}

	private function getFallDamageReduction(int $rarity): float {
		return match($rarity) {
				self::RARITY_COMMON => 0.3,
				self::RARITY_UNCOMMON, self::RARITY_RARE => 0.4,
				default => 0.5
		};
	}

	private function getEggDropChance(int $rarity): float {
		return match($rarity) {
			self::RARITY_RARE => 0.8,
			self::RARITY_EPIC, self::RARITY_LEGENDARY => 1,
			default => 0,
		};
	}

	public function getMaxLevel() : int{
		return 100;
	}

	public function onUse(Player $player, PetInstance $instance, Event $event) : void{
		if($event instanceof EntityDeathEvent && mt_rand(1, 100) === 1){
			$drops = $event->getDrops();
			$drops[] = VanillaItems::EGG();
			$event->setDrops($drops);
		}

		if($event instanceof EntityDamageByEntityEvent){
			$event->setBaseDamage($event->getBaseDamage() * (100 - $this->getFallDamageReduction($instance->getRarity()) * $instance->getLevel()));
		}
	}

	public function tryCall(Event $event) : void{
		if($event instanceof EntityDeathEvent){
			$e = $event->getEntity();
			$last = $e->getLastDamageCause();


			if($last instanceof EntityDamageByEntityEvent&& $e::getNetworkTypeId() === EntityIds::CHICKEN){
				$damager = $last->getDamager();
				if(!$damager instanceof AetherPlayer) return;

				if($damager->getPetData()->getActivePetId() !== $this->getIdentifier()) return;

				$pet = $damager->getPetData()->getPetCollection()[$this->getIdentifier()];

				if($pet?->getPet() instanceof $this){
					$this->onUse($damager, $pet, $event);
				}
			}
		}

		if($event instanceof EntityDamageEvent && $event->getCause() === EntityDamageEvent::CAUSE_FALL){
			$e = $event->getEntity();

			if(!$e instanceof Player) return;

			$pet = PetInstance::fromItem($e->getInventory()->getItemInHand());

			if($pet?->getPet() instanceof $this){
				$this->onUse($e, $pet, $event);
			}
		}
	}

	public function onActivate(Player $player, PetInstance $pet) : bool{
		return true;
	}

	public function getSkillType() : int{
		return self::TYPE_COMBAT;
	}

	public function getEntityId() : string{
		return EntityIds::CHICKEN;
	}
}