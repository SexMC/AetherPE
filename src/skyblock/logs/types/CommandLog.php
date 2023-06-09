<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\player\Player;
use skyblock\logs\Log;

class CommandLog extends Log {



	public function __construct(Player $player, string $message){
		$this->data["player"] = $player->getName();
		$this->data["command"] = $message;
	}


	public function getType() : string{
		return "command";
	}
}