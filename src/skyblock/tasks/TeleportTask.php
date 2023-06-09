<?php

declare(strict_types=1);

namespace skyblock\tasks;

use pocketmine\player\Player;
use pocketmine\scheduler\Task;

class TeleportTask extends Task {

	public function __construct(
		private Player $player,
		private Location $location,
	){ }

	public function onRun() : void{
		// TODO: Implement onRun() method.
	}
}