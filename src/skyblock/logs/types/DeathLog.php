<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\player\Player;
use skyblock\logs\Log;

class DeathLog extends Log {

	public function __construct(Player $player, string $reason, string $killer = "", array $drops = []){
		$this->data["player"] = $player->getName();
		$this->data["reason"] = $reason;
		$this->data["drops"] = json_encode($drops);
		$this->data["killer"] = $killer;
	}

	public function getType() : string{
		return "death";
	}
}