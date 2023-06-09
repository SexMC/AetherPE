<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use skyblock\commands\AetherCommand;
use skyblock\Main;
use skyblock\tasks\RestartTask;
use skyblock\utils\TimeUtils;
use skyblock\utils\Utils;

class RestartCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View when the servers will restart");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$time = TimeUtils::getFormattedTime(RestartTask::$timeLeft);

		$sender->sendMessage(Main::PREFIX . "§c" . Utils::getServerName() . "§7 will restart in §c$time");
	}
}