<?php

declare(strict_types=1);

namespace skyblock\items\pets;

use Closure;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\ShulkerBoxCloseSound;
use pocketmine\world\sound\ShulkerBoxOpenSound;
use skyblock\entity\pet\PetEntity;
use skyblock\events\skills\SkillGainXpEvent;
use skyblock\items\pets\types\ChickenPet;
use skyblock\items\pets\types\SilverfishPet;
use skyblock\items\pets\types\SkeletonPet;
use skyblock\items\pets\types\SpiderPet;
use skyblock\items\pets\types\WolfPet;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\player\CustomPlayerInventoryListener;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\StringCooldownTrait;

class PetHandler {
	use AetherHandlerTrait;
	use StringCooldownTrait;

	/** @var array<string, int> */
	private array $activated = [];
	/** @var Pet[] */
	private array $pets = [];
	/** @var PetEntity[] */
	private array $cachedPetEntities = [];

	public function onEnable() : void{


		$this->plugin->getServer()->getPluginManager()->registerEvent(
			PlayerInteractEvent::class,
			Closure::fromCallable([$this, "onInteract"]),
			EventPriority::NORMAL,
			$this->plugin,
			false
		);

		$this->plugin->getServer()->getPluginManager()->registerEvent(
			PlayerItemUseEvent::class,
			Closure::fromCallable([$this, "onInteract"]),
			EventPriority::NORMAL,
			$this->plugin,
			false
		);

		$this->plugin->getServer()->getPluginManager()->registerEvent(
			SkillGainXpEvent::class,
			Closure::fromCallable([$this, "onSkillXpGain"]),
			EventPriority::NORMAL,
			$this->plugin,
			false
		);

		$this->registerPet(new ChickenPet());
		$this->registerPet(new SilverfishPet());
		$this->registerPet(new SpiderPet());
		$this->registerPet(new WolfPet());
		$this->registerPet(new SkeletonPet());
	}

	private function registerPet(Pet $pet): void {
		Main::debug("Registered pet ({$pet->getName()})");
		foreach($pet->getDesiredEvents() as $event) {
			$this->plugin->getServer()->getPluginManager()->registerEvent(
				$event,
				Closure::fromCallable([$pet, "tryCall"]),
				$pet->getPriority(),
				$this->plugin,
				false
			);
		}

		$this->pets[strtolower($pet->getIdentifier())] = $pet;
	}

	public function onSkillXpGain(SkillGainXpEvent $event): void {
		$player = $event->getPlayer();
		$xp = $event->getXp();


		$pet = $player->getPetData()->getPetCollection()[$player->getPetData()->getActivePetId()] ?? null;

		if(!$pet instanceof PetInstance) return;

		if($pet->getLevel() >= 100) return;


		$increase = 0.25;
		if($pet->getPet()->getSkillClassByType() === $event->getSkill()::id()){
			$increase = 1;
		}

		$xp = (int) floor($xp * $increase);

		$pet->setXp($pet->getXp() + (int) floor($xp));

		if($pet->getXp() >= Pet::getXpNeeded($pet->getRarity(), $pet->getLevel()+1)){
			$pet->setXp($pet->getXp() - Pet::getXpNeeded($pet->getRarity(), $pet->getLevel()+1));
			$pet->setLevel($pet->getLevel() + 1);
			//pet lvlup

			$string = $pet->getPet()->getColor($pet->getRarity()) . $pet->getPet()->getName();
			$player->sendMessage(Main::PREFIX . "Your §l$string §r§7pet has leveled up!");
		}

	}

	public function onInteract(PlayerInteractEvent|PlayerItemUseEvent $event): void {
		$player = $event->getPlayer();
		$item = $event->getItem();

		$type = Pet::getId($item);

		assert($player instanceof AetherPlayer);

		if(!$type instanceof Pet) return;

		$event->cancel();

		$level = Pet::getLevel($item);
		$uuid = Pet::getXuid($item);
		$rarity = Pet::getRarity($item);
		$xp = Pet::getXp($item);

		$allPets = $player->getPetData()->getPetCollection();
		if(isset($allPets[$type->getIdentifier()])){
			$player->sendMessage(Main::PREFIX . "You already have a §c{$type->getName()}§7 pet in your pet collection.");
            $player->broadcastSound(new ShulkerBoxCloseSound());
			return;
		}

		$item->pop();
		$player->getInventory()->setItemInHand($item);
			
		$allPets[$type->getIdentifier()] = new PetInstance($type, $level, $uuid, $rarity, $xp);
		$player->getPetData()->setPetCollection($allPets);
        $player->sendMessage(Main::PREFIX . "§c{$type->getItem($level,$rarity, 0, 0)->getName()} §7has been added to your pet collection");
        $player->broadcastSound(new ShulkerBoxOpenSound());
    }


	//this function spawns/removes the pet entity
	//if it is forceRemove true then it will despawn the pet no matter what conditions
	public function updatePets(AetherPlayer $player, bool $forceDespawn = false): void {
		$active = $player->getPetData()->getActivePetId();
		if($active === null || $forceDespawn === true){
			 if(isset($this->cachedPetEntities[$player->getName()])){
				 $e = $this->cachedPetEntities[$player->getName()];
				 $e->instance->getPet()->onDeActivate($player, $e->instance);
				 CustomPlayerInventoryListener::checkPvE($player, $e->instance->buildPetItem(), VanillaItems::AIR());


				 $e->glass->flagForDespawn();
				 $e->flagForDespawn();

				 unset($this->cachedPetEntities[$player->getName()]);
			 }

			 return;
		}

		$all = $player->getPetData()->getPetCollection();
		if(!isset($all[$active])) return;

		$current = $all[$active];
		if(isset($this->cachedPetEntities[$player->getName()])){
			$e = $this->cachedPetEntities[$player->getName()];
			$e->instance->getPet()->onDeActivate($player, $e->instance);

			if(!$e->isFlaggedForDespawn() && !$e->isClosed()){
				$e->glass->flagForDespawn();
				$e->flagForDespawn();

				CustomPlayerInventoryListener::checkPvE($player, $e->instance->buildPetItem(), VanillaItems::AIR());

			}
		}

		$current->getPet()->onActivate($player, $current);

		$e = new PetEntity($player, $current->getPet()->getEntityId(), $current, $player->getLocation());
		$e->spawnToAll();

		CustomPlayerInventoryListener::checkPvE($player, VanillaItems::AIR(), $current->buildPetItem());



		$this->cachedPetEntities[$player->getName()] = $e;
	}


	/**
	 * @return Pet[]
	 */
	public function getAllPets() : array{
		return $this->pets;
	}

	public function getPet(string $identifier): ?Pet {
		return $this->pets[strtolower($identifier)] ?? null;
	}



	public function onDisable() : void{
		foreach($this->pets as $pet) {
			$pet->onClose($this->plugin);
		}
	}
}