<?php

declare(strict_types=1);

namespace skyblock\items\vanilla;

use pocketmine\item\Axe;
use pocketmine\item\Hoe;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\item\ToolTier;
use ReflectionClass;

class VanillaItemHandler {

	const ITEM_NETHERITE_SWORD = 743;
	const ITEM_NETHERITE_SHOVEL = 744;
	const ITEM_NETHERITE_PICKAXE = 745;
	const ITEM_NETHERITE_AXE = 746;
	const ITEM_NETHERITE_HOE = 747;

	const ITEM_ECHO_SHARD = 779;


	public function __construct(){
		$class = new ReflectionClass(ToolTier::class);
		$register = $class->getMethod('register');
		$register->setAccessible(true);
		$constructor = $class->getConstructor();
		$constructor->setAccessible(true);
		$instance = $class->newInstanceWithoutConstructor();
		$constructor->invoke($instance, 'netherite', 6, 2031, 9, 10);
		$register->invoke(null, $instance);

		ItemFactory::getInstance()->register(new Sword(new ItemIdentifier(self::ITEM_NETHERITE_SWORD, 0), 'Netherite Sword', ToolTier::NETHERITE()));
		ItemFactory::getInstance()->register(new Shovel(new ItemIdentifier(self::ITEM_NETHERITE_SHOVEL, 0), 'Netherite Shovel', ToolTier::NETHERITE()));
		ItemFactory::getInstance()->register(new Pickaxe(new ItemIdentifier(self::ITEM_NETHERITE_PICKAXE, 0), 'Netherite Pickaxe', ToolTier::NETHERITE()));
		ItemFactory::getInstance()->register(new Axe(new ItemIdentifier(self::ITEM_NETHERITE_AXE, 0), 'Netherite Axe', ToolTier::NETHERITE()));
		ItemFactory::getInstance()->register(new Hoe(new ItemIdentifier(self::ITEM_NETHERITE_HOE, 0), 'Netherite Hoe', ToolTier::NETHERITE()));
	}
}