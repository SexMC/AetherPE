<?php

declare(strict_types=1);

namespace skyblock\events;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\itemmods\ItemMod;

class CustomEntityDamageByEntityEvent extends EntityDamageByEntityEvent {

    private float $damage;

    /** @var array<string, int> */
    private array $damageAmplifiers = [];
    /** @var array<string, int> */
    private array $damageReducers = [];
    /** @var array<string, int> */
    private array $damageMultipliers = [];
    /** @var array<string, int> */
    private array $damageDividers = [];
	/** @var array[array<ce, damager, entity>] */
	private array $reflecting = [];

	private ?Entity $child = null;

	private CustomEnchantsReactionManager $damagerCustomEnchantsReactionManager;
	private CustomEnchantsReactionManager $entityCustomEnchantsReactionManager;

	private ItemModReactionManager $damagerItemModReactionManager;
	private ItemModReactionManager $entityItemModReactionManager;

    public function __construct(Entity $damager, Entity $entity, int $cause, float $damage, array $modifiers = [], float $knockBack = 0.4, ?Entity $child = null) {
        parent::__construct($damager, $entity, $cause, $damage, $modifiers, $knockBack);
        $this->damage = $damage;
		$this->child = $child;

		$this->damagerCustomEnchantsReactionManager = new CustomEnchantsReactionManager($damager, []);
		$this->entityCustomEnchantsReactionManager = new CustomEnchantsReactionManager($entity, []);
		$this->damagerItemModReactionManager = new ItemModReactionManager($damager, []);
		$this->entityItemModReactionManager = new ItemModReactionManager($entity, []);
    }

	public function getChild() : ?Entity{
		return $this->child;
	}

    public function increaseDamage(float $damage, string $cause): void {
        $this->damageAmplifiers[$cause] = $damage;
    }

    public function decreaseDamage(float $damage, string $cause): void {
        $this->damageReducers[$cause] = $damage;
    }

    public function multiplyDamage(float $multiplier, string $cause): void {
        $this->damageMultipliers[$cause] = $multiplier;
    }

    public function divideDamage(float $divider, string $cause): void {
        $this->damageDividers[$cause] = $divider;
    }

	public function getFinalDamage() : float{
		/*if($this->getEntity() instanceof Player){
			$modifier = EntityDamageEvent::MODIFIER_ARMOR;

			foreach ($this->getEntity()->getArmorInventory()->getContents() as $item) {
				if ($item->getNamedTag()->getByte("heroic", 0) === 1) {
					$this->setModifier($this->getModifier($modifier) - 1.2, $modifier);
				}
			}
		}*/

        $damage = parent::getFinalDamage();
        $damage += array_sum($this->damageAmplifiers);
        $damage -= array_sum($this->damageReducers);
        $damage *= array_sum($this->damageMultipliers) + 1;
        $damage /= array_sum($this->damageDividers) + 1;

        return min(18, $damage);
    }




	/**
	 * @return CustomEnchantsReactionManager
	 */
	public function getDamagerCustomEnchantsReactionManager() : CustomEnchantsReactionManager{
		return $this->damagerCustomEnchantsReactionManager;
	}

	/**
	 * @return CustomEnchantsReactionManager
	 */
	public function getEntityCustomEnchantsReactionManager() : CustomEnchantsReactionManager{
		return $this->entityCustomEnchantsReactionManager;
	}

	/**
	 * @return ItemModReactionManager
	 */
	public function getEntityItemModReactionManager() : ItemModReactionManager{
		return $this->entityItemModReactionManager;
	}

	/**
	 * @return ItemModReactionManager
	 */
	public function getDamagerItemModReactionManager() : ItemModReactionManager{
		return $this->damagerItemModReactionManager;
	}

	/**
	 * @return array
	 */
	public function getReflecting() : array{
		return $this->reflecting;
	}

	public function reflectFromDamagerToEntity(CustomEnchantInstance $ce): void {
		$this->damagerCustomEnchantsReactionManager->remove($ce->getCustomEnchant()::class);
		$this->reflecting[] = [$ce, $this->getEntity(), $this->getDamager()];
	}

	public function reflectFromEntityToDamager(CustomEnchantInstance $ce): void {
		$this->entity->remove($ce->getCustomEnchant()::class);
		$this->reflecting[] = [$ce, $this->getDamager(), $this->getEntity()];
	}

	public function __toString() : string{
		$string = "Damager:§c " . ($this->getDamager() instanceof Player ? $this->getDamager()->getName() : "Not a player");
		$string .= "\nEntity:§c " . ($this->getEntity() instanceof Player ? $this->getEntity()->getName() : "Not a player");
		$string .= "\nBase Damage (without modifiers):§c " . $this->getOriginalBaseDamage();
		$string .= "\nFinal Damage:§c " . $this->getFinalDamage();

		$string .= "\nDamage Increases: ";
		foreach($this->damageMultipliers as $k => $v){
			$string .= "\n$k:§c +$v x damage";
		}
		foreach($this->damageAmplifiers as $k => $v){
			$string .= "\n$k:§c +$v damage";
		}

		$string .= "\nDamage Reducers: ";
		foreach($this->damageDividers as $k => $v){
			$string .= "\n$k:§c -$v x damage";
		}
		foreach($this->damageReducers as $k => $v){
			$string .= "\n$k:§c -$v damage";
		}

		if($this->getEntity() instanceof Player){
			$string .= "\nEntity procced ces: " . implode(", ", array_map(fn($c) => ($c instanceof CustomEnchantInstance ? ($c->getCustomEnchant()->getRarity()->getColor() . $c->getCustomEnchant()->getIdentifier()->getName() . "§r") : "error"), $this->getEntityCustomEnchantsReactionManager()->getActivating()));
		} else $string .= "\nEntity procced ces:§c ENTITY IS NOT A PLAYER";

		if($this->getEntity() instanceof Player){
			$string .= "\nEntity procced item mods: " . implode(", ", array_map(fn(ItemMod $c) => $c->getFormat() . "§r", $this->getEntityItemModReactionManager()->getActivating()));
		} else $string .= "\nEntity procced item mods:§c ENTITY IS NOT A PLAYER";


		if($this->getDamager() instanceof Player){
			$string .= "\nDamager procced ces: " . implode(", ",  array_map(fn($c) => ($c instanceof CustomEnchantInstance ? ($c->getCustomEnchant()->getRarity()->getColor() . $c->getCustomEnchant()->getIdentifier()->getName() . "§r") : "error"), $this->getDamagerCustomEnchantsReactionManager()->getActivating()));
		} else $string .= "\nEntity procced ces:§c DAMAGER IS NOT A PLAYER";

		if($this->getEntity() instanceof Player){
			$string .= "\nDamager procced item mods: " . implode(", ", array_map(fn(ItemMod $c) => $c->getFormat() . "§r", $this->getDamagerItemModReactionManager()->getActivating()));
		} else $string .= "\nDamager procced item mods: §cDAMAGER IS NOT A PLAYER";


		return $string;
	}
}