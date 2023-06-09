<?php

declare(strict_types=1);

namespace skyblock\caches\data;

use pocketmine\utils\SingletonTrait;
use skyblock\traits\AetherSingletonTrait;
use skyblock\traits\PlayerCooldownTrait;
use skyblock\traits\InstanceTrait;
use skyblock\traits\StringIntCache;

//local playtime cache
class MenuCache {
	use StringIntCache;
	use AetherSingletonTrait;

}