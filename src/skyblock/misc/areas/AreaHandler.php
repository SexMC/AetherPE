<?php

declare(strict_types=1);

namespace skyblock\misc\areas;

use skyblock\traits\AetherHandlerTrait;

class AreaHandler {
	use AetherHandlerTrait;

	private static array $areas = [];

	public function onEnable() : void{
		self::add(new Area("Spider's Den", -320, 30, -97, -148, 180, -378));
		self::add(new Area("The Barn", 80, 135, -197, 222, 38, -327));
		self::add(new Area("Mushroom Dessert", 85, 200, -328, 388, 8, -627));
		self::add(new Area("Gold Mine", 50, 140, -264, -104, 24, -396));
		self::add(new Area("The Park", -276, 160, 57, -476, 68, -134));
		self::add(new Area("The End", -480, 3, -137, -804, 190, -348));
	}

	public static function add(Area $area): void {
		self::$areas[$area->getName()] = $area;
	}
	public static function getAllAreas() : array{
		return self::$areas;
	}


	public static function SPIDERS_DEN(): Area {
		return self::$areas["Spider's Den"];
	}

	public static function THE_BARN(): Area {
		return self::$areas["The Barn"];
	}

	public static function MUSHROOM_DESSERT(): Area {
		return self::$areas["Mushroom Dessert"];
	}

	public static function GOLD_MINE(): Area {
		return self::$areas["Gold Mine"];
	}

	public static function THE_END(): Area {
		return self::$areas["The End"];
	}

	public static function THE_PARK(): Area {
		return self::$areas["The Park"];
	}
}