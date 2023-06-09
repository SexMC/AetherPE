<?php

declare(strict_types=1);

namespace skyblock\items\customenchants;

use pocketmine\utils\RegistryTrait;

/**
 * @method static BaseCustomEnchant SCOTOPIA()
 * @method static BaseCustomEnchant ANGLER()
 * @method static BaseCustomEnchant AUTO_SMELT()
 * @method static BaseCustomEnchant BANE_OF_ARTHROPODS()
 * @method static BaseCustomEnchant BLAST_PROTECTION()
 * @method static BaseCustomEnchant CASTER()
 * @method static BaseCustomEnchant CLEAVE()
 * @method static BaseCustomEnchant CRITICAL()
 * @method static BaseCustomEnchant CUBISM()
 * @method static BaseCustomEnchant EFFICIENCY()
 * @method static BaseCustomEnchant ENDER_SLAYER()
 * @method static BaseCustomEnchant EXECUTE()
 * @method static BaseCustomEnchant EXPERIENCE()
 * @method static BaseCustomEnchant FIRST_STRIKE()
 * @method static BaseCustomEnchant GROWTH()
 * @method static BaseCustomEnchant HARVESTING()
 * @method static BaseCustomEnchant KNOCKBACK()
 * @method static BaseCustomEnchant LURE()
 * @method static BaseCustomEnchant MAGNET()
 * @method static BaseCustomEnchant POWER()
 * @method static BaseCustomEnchant PUNCH()
 * @method static BaseCustomEnchant PROTECTION()
 * @method static BaseCustomEnchant RAINBOW()
 * @method static BaseCustomEnchant RESPIRATION()
 * @method static BaseCustomEnchant SCAVENGER()
 * @method static BaseCustomEnchant SHARPNESS()
 * @method static BaseCustomEnchant SILK_TOUCH()
 * @method static BaseCustomEnchant SMITE()
 * @method static BaseCustomEnchant THUNDERLORD()
 */
final class CustomEnchants{
	use RegistryTrait;

	protected static function setup() : void{
		$factory = CustomEnchantFactory::getInstance();

		foreach($factory->getList() as $key => $ce) {
			self::register($key, $ce);
		}
	}

	protected static function register(string $name, BaseCustomEnchant $item) : void{
		self::_registryRegister($name, $item);
	}
}