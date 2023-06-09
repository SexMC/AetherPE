<?php

declare(strict_types=1);

namespace skyblock\entity;

use pocketmine\network\mcpe\protocol\types\entity\EntityIds as Ids;
use pocketmine\utils\RegistryTrait;

/**
 * @method static LivingEntityType BLAZE()
 * @method static LivingEntityType ZOMBIE()
 */
class EntityTypes {
    use RegistryTrait;

    protected static function setup(): void {
        self::register("blaze", new LivingEntityType(Ids::BLAZE, "Blaze", 20, 1.8, 0.6));
        self::register("zombie", new LivingEntityType(Ids::ZOMBIE, "Zombie", 20, 1.95, 0.6));
    }

    protected static function register(string $name, LivingEntityType $type): void {
        self::_registryRegister($name, $type);
    }

    public function get(string $name): ?LivingEntityType {
        try {
            $entity = self::_registryFromString($name);
            if ($entity instanceof LivingEntityType) {
                return $entity;
            }
        } catch (\InvalidArgumentException) {
        }

        return null;
    }
}