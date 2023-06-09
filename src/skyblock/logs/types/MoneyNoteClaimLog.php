<?php

declare(strict_types=1);

namespace skyblock\logs\types;

use pocketmine\player\Player;
use skyblock\logs\Log;

class MoneyNoteClaimLog extends Log {

	public function __construct(Player $player, int $amount, string $signer){
		$this->data["player"] = $player->getName();
		$this->data["amount"] = $amount;
		$this->data["signer"] = $signer;
	}

	public function getType() : string{
		return "money_claim";
	}
}