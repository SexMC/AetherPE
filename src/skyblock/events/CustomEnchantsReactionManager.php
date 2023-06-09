<?php

declare(strict_types=1);

namespace skyblock\events;

use pocketmine\entity\Entity;
use skyblock\items\customenchants\CustomEnchantInstance;

class CustomEnchantsReactionManager {

	protected Entity $entity;
    /** @var \skyblock\items\customenchants\CustomEnchantInstance[] */
    protected array $activating = [];
    /** @var array<string, array> */
    protected array $negated = [];

    public function __construct(Entity $entity, array $activating) {
        $this->entity = $entity;
        $this->activating = $activating;
    }

    /**
     * @return \skyblock\items\customenchants\CustomEnchantInstance[]
     */
    public function getActivating(): array {
        return $this->activating;
    }

    public function isActivating(string $class): bool {
        return isset($this->activating[$class]);
    }

    public function add(CustomEnchantInstance $enchantInstance): void {
        $this->activating[$enchantInstance->getCustomEnchant()::class] = $enchantInstance;
    }

	public function set(array $customenchantInstances): void {
		$this->activating = $customenchantInstances;
	}

    public function remove(string $class): void {
        if (isset($this->activating[$class])) {
            unset($this->activating[$class]);
        }
    }

    public function getNegated(): array {
        return $this->negated;
    }

    public function isNegated(string $class): bool {
        return isset($this->negated[$class]);
    }

    public function getNegatedBy(string $class): array {
        return $this->negated[$class] ?? [];
    }

	/**
	 * @param string $class -> class that's getting negated
	 * @param string $negation -> the class that negated it
	 */
    public function negate(string $class, string $negation): void {
        if (isset($this->negated[$class])) {
            $this->negated[$class][] = $negation;
        } else $this->negated[$class] = [$negation];
    }

    public function removeNegated(string $class, string $negation): void {
        if (isset($this->negated[$class]) && in_array($negation, $this->negated[$class])) {
            unset($this->negated[$class][array_search($negation, $this->negated[$class])]);

            if (empty($this->negated[$class])) {
                unset($this->negated[$class]);
            }
        }
    }
}