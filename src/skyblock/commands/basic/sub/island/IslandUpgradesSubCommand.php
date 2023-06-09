<?php

declare(strict_types=1);

namespace skyblock\commands\basic\sub\island;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherSubCommand;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\menus\island\IslandUpgradeMenu;
use skyblock\sessions\Session;
use skyblock\utils\IslandUtils;
use skyblock\utils\Utils;

class IslandUpgradesSubCommand extends AetherSubCommand {
	protected function prepare() : void{
		$this->setDescription("Upgrade your island");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$session = new Session($sender);
			$island = $session->getIslandOrNull();

			if($island === null){
				$sender->sendMessage(Main::PREFIX . "You're not in an island");
				return;
			}

			(new IslandUpgradeMenu($island))->send($sender);
		}
	}
}