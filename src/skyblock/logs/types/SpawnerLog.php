<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\player\Player;
use pocketmine\world\Position;
use skyblock\logs\Log;

class SpawnerLog extends Log {

	const ACTION_BREAK = "broke";
	const ACTION_PLACE = "placed";

	public function __construct(Player $player, string $spawner, string $action, Position $position, int $count = 1){
		$this->data["player"] = $player->getName();
		$this->data["spawner"] = $spawner;
		$this->data["action"] = $action;
		$this->data["count"] = $count;

		$this->data["x"] = $position->getFloorX();
		$this->data["y"] = $position->getFloorY();
		$this->data["z"] = $position->getFloorZ();
		$this->data["world"] = $position->getWorld()->getDisplayName();
	}

	public function getType() : string{
		return "spawner";
	}
}