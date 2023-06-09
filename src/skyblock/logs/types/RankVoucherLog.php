<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\player\Player;
use skyblock\logs\Log;

class RankVoucherLog extends Log {

	public function __construct(Player $player, string $rank){
		$this->data["player"] = $player->getName();
		$this->data["rank"] = $rank;
	}

	public function getType() : string{
		return "rank";
	}
}