<?php

declare(strict_types=1);

namespace skyblock\items\vanilla;

use pocketmine\entity\Location;
use pocketmine\entity\projectile\Throwable;
use pocketmine\item\SplashPotion as PMSplashPotion;
use pocketmine\player\Player;
use skyblock\entity\projectile\SplashPotion as EntitySplashPotion;

class SplashPotion extends PMSplashPotion {
    protected function createEntity(Location $location, Player $thrower): Throwable{
        return new EntitySplashPotion($location, $thrower, $this->getType());
    }
}