<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\player\Player;
use skyblock\islands\Island;
use skyblock\logs\Log;

class BankLog extends Log {

	public function __construct(Player $player, Island $island, int $count, string $what){
		$this->data["player"] = $player->getName();
		$this->data["island"] = $island->getName();
		$this->data["count"] = $count;
		$this->data["what"] = $what;
	}

	public function getType() : string{
		return "bank";
	}
}