<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types\reward;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Language;
use pocketmine\player\Player;
use pocketmine\Server;

class QuestCommandReward extends QuestReward {

	public function __construct(private string $command){ }

	public function give(Player $player) : void{
		$sender = new ConsoleCommandSender(Server::getInstance(), new Language(Language::FALLBACK_LANGUAGE));
		$cmd = str_replace("{player}", $player->getName(), $this->command);

		$player->getServer()->dispatchCommand($sender, $cmd);
	}
}