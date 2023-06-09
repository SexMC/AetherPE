<?php

declare(strict_types=1);

namespace skyblock\misc\warps;

use pocketmine\math\Vector3;
use pocketmine\utils\TextFormat;
use skyblock\caches\pvpzones\PvpZone;
use skyblock\Database;
use skyblock\traits\AetherSingletonTrait;
use skyblock\utils\Queries;

class WarpHandler {
	use AetherSingletonTrait;

	/**
	 * @return Warp[]
	 */
	public function getAllWarps() {
		$data = yield Database::getInstance()->getLibasynql()->asyncSelect(Queries::WARP_GET_ALL);
		$list = [];

		foreach($data as $datum){
			$name = $datum["name"];
			$server = $datum["server"];
			$world = $datum["world"];
			$vector = new Vector3(...json_decode($datum["pos"], true));
			$open = (bool) $datum["open"];

			$list[TextFormat::clean($name)] = new Warp($name, $world, $server, $vector, $open);
		}


		return $list;
	}
}