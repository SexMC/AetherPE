<?php

declare(strict_types=1);

namespace skyblock\items\special;

use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use skyblock\items\special\types\ArachnesCalling;
use skyblock\items\special\types\CarrotCandy;
use skyblock\items\special\types\CustomEnchantmentBook;
use skyblock\items\special\types\fishing\BlessedBait;
use skyblock\items\special\types\fishing\FishBait;
use skyblock\items\special\types\fishing\MinnowBait;
use skyblock\items\special\types\fishing\SpikedBait;
use skyblock\items\special\types\fishing\WhaleBait;
use skyblock\items\special\types\minion\BioMinionFuel;
use skyblock\items\special\types\minion\CoalMinionFuel;
use skyblock\items\special\types\minion\DieselMinionFuel;
use skyblock\items\special\types\minion\MinionEgg;

use skyblock\items\special\types\SeaBossEggItem;
use skyblock\items\special\types\SpawnerItem;
use skyblock\items\special\types\upgrades\UltimateCarrotCandyUpgrade;

class SpecialItemHandler{

	/** @var SpecialItem[] */
	private static array $items = [];

	public function __construct(){
		foreach(self::registerItems() as $item){
			self::registerItem($item);
		}
	}

	private static function registerItem(SpecialItem $specialItem) : void{
		self::$items[strtolower($specialItem::getItemTag())] = $specialItem;
	}

	public static function call(string $tag, Player $player, Event $event, Item $item) : void{
		if(isset(self::$items[strtolower($tag)])){
			self::$items[strtolower($tag)]->onUse($player, $event, $item);
		}
	}

	public static function isSpecialItem(Item $item) : bool{
		return $item->getNamedTag()->getString(SpecialItem::TAG_SPECIAL_ITEM, "") !== "";
	}

	public static function registerItems() : array{
		return [




			new SeaBossEggItem(),
			new BlessedBait(),
			new FishBait(),
			new MinnowBait(),
			new SpikedBait(),
			new WhaleBait(),

			//new SpawnerItem(),

			//new SkyblockMenuItem(),


			new BioMinionFuel(),
			new CoalMinionFuel(),
			new DieselMinionFuel(),
			new MinionEgg(),
			//new AutoSmelter(),
			//new Compactor(),
			//new EnchantedLavaBucket(),
			//new DiamondSpreading(),
			//new EnchantedHopper(),
			//new BudgetHopper(),


			//RECIPE ITEMS


			//new ArachneFragment(),




			//new CarrotCandy(),
			//new CustomEnchantmentBook(),
			//new PersonalCompactorItem(),
		//	new HyperFurnace(),
			//new HotpotatoBook(),


			//new ArachnesCalling(),

			//new UltimateCarrotCandyUpgrade(),
		];
	}

	public static function getItem(string $item) : ?SpecialItem{
		return self::$items[strtolower($item)] ?? null;
	}

	/**
	 * @param string $class
	 *
	 * @return ReflectionParameter[]
	 * @throws ReflectionException
	 */
	public static function getArguments(string $class) : array{
		$array = [];

		$class = new ReflectionClass($class);
		foreach($class->getMethod("getItem")->getParameters() as $p){
			$array[] = $p;
		}

		return $array;
	}

	/**
	 * @return SpecialItem[]
	 */
	public static function getItems() : array{
		return self::$items;
	}
}