<?php

declare(strict_types=1);

namespace skyblock\events\economy;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class PlayerXpSpendEvent extends PlayerEvent {

	protected float $amount;

	public function __construct(Player $player, float $amount){
		$this->player = $player;
		$this->amount = $amount;
	}

	public function getAmount() : float{
		return $this->amount;
	}

}