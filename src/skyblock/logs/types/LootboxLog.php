<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\player\Player;
use skyblock\logs\Log;

class LootboxLog extends Log {

	public function __construct(Player $player, string $lb, array $items){
		$this->data["player"] = $player->getName();
		$this->data["lootbox"] = $lb;
		$this->data["items"] = json_encode($items);
	}

	public function getType() : string{
		return "lootbox";
	}
}