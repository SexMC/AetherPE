<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\island;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\forms\island\top\IslandTopForm;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\IslandUtils;
use skyblock\utils\Utils;

class IslandTopSubArgument extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("View top islands");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$sender->sendForm(new IslandTopForm());
		}
	}
}