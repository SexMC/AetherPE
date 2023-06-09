<?php

declare(strict_types=1);

namespace skyblock\items\special\types\fishing;

use skyblock\events\player\PlayerFishEvent;
use skyblock\events\player\PlayerPreFishEvent;
use skyblock\items\special\SpecialItem;

//TODO: need to make it so it consumes and calls the methods and test this
abstract class Bait extends SpecialItem {

	public abstract function onPreFish(PlayerPreFishEvent $event): bool;
	public abstract function onFish(PlayerFishEvent $event): bool;
}