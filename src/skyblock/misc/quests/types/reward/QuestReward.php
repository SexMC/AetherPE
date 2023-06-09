<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types\reward;

use pocketmine\player\Player;

abstract class QuestReward{

	public abstract function give(Player $player): void;


}