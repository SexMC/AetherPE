<?php

declare(strict_types=1);

namespace skyblock\forms\island\unlockables;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\forms\island\management\IslandManageForm;
use skyblock\islands\Island;
use skyblock\misc\arena\ArenaManager;
use skyblock\sessions\Session;

class IslandUnlockablesForm extends MenuForm {
	public function __construct(){
		parent::__construct("Unlockables", "Select an option", [new MenuOption("<- Back"), new MenuOption("Spawners")], function(Player $player, int $btn): void {
			if($btn === 1){
				$is = new Island((new Session($player))->getIslandName());

				if(($id = $is->getRedis()->get("island.{$is->getName()}.boss.arena.id"))){
					if(($arena = ArenaManager::getInstance()->getArena($id))){
						$player->sendForm(new SpawnerUnlockablesViewForm($is->getRedis()->get("island.{$is->getName()}.boss.arena.boss"), $is));
						return;
					}
				}

				$player->sendForm(new SpawnerUnlockablesForm($is));
				return;
			}

			$player->sendForm(new IslandManageForm($player, new Island((new Session($player))->getIslandName())));
		});
	}
}