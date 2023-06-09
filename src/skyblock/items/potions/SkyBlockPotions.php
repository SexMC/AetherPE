<?php

declare(strict_types=1);

namespace skyblock\items\potions;

use pocketmine\item\ItemIds;
use pocketmine\utils\RegistryTrait;
use skyblock\items\ability\HealAbility;
use skyblock\items\potions\types\CriticalPotion;
use skyblock\items\potions\types\HastePotion;
use skyblock\items\potions\types\HealthPotion;
use skyblock\items\potions\types\ManaPotion;
use skyblock\items\potions\types\SpeedPotion;
use skyblock\items\potions\types\StunPotion;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemFactory;
use skyblock\traits\AetherHandlerTrait;

/**
 * @method static SpeedPotion SPEED()
 * @method static ManaPotion MANA()
 * @method static HealthPotion HEALTH()
 * @method static HastePotion HASTE()
 * @method static CriticalPotion CRITICAL()
 * @method static StunPotion STUN()
 */
final class SkyBlockPotions{
	use RegistryTrait;

	protected static function setup() : void{
		//AetherPotionHandler::getInstance();
		$factory = SkyblockItemFactory::getInstance();
		AetherPotionHandler::initialise();

		self::register("speed", $factory->get(ItemIds::POTION, 80));
		self::register("mana", $factory->get(ItemIds::POTION, 81));
		self::register("health", $factory->get(ItemIds::POTION, 82));
		self::register("haste", $factory->get(ItemIds::POTION, 83));
		self::register("critical", $factory->get(ItemIds::POTION, 84));
		self::register("stun", $factory->get(ItemIds::POTION, 85));

	}

	protected static function register(string $name, SkyblockItem $item) : void{
		self::_registryRegister($name, $item);
	}

	public static function __callStatic($name, $arguments){
		if(count($arguments) > 0){
			throw new \ArgumentCountError("Expected exactly 0 arguments, " . count($arguments) . " passed");
		}
		try{
			return clone self::_registryFromString($name);
		}catch(\InvalidArgumentException $e){
			throw new \Error($e->getMessage(), 0, $e);
		}
	}
}