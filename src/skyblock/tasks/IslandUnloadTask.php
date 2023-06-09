<?php

declare(strict_types=1);

namespace skyblock\tasks;

use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use skyblock\islands\Island;
use skyblock\islands\IslandHandler;
use skyblock\utils\IslandUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class IslandUnloadTask extends Task {

	public function onRun() : void{
		foreach(IslandUtils::getLoadedIslands(Server::getInstance()) as $n){
			$island = new Island($n);

			if(empty($island->getOnlineMembers(true))){
				if(($world = $island->getWorld())){
					if(!empty($world->getPlayers())) continue;
				}

				Await::f2c(function() use($island){
					yield IslandHandler::getInstance()->saveIsland($island);
				});
			}
		}
	}
}