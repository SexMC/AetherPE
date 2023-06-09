<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\items\rarity\special\types\PlayerSkullItem;
use skyblock\misc\warps\WarpHandler;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class SpawnCommand extends AetherCommand {


	protected function prepare() : void{
		$this->setDescription("Go to spawn");

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			Await::f2c(function() use($sender){
				if(!Utils::isHubServer()){
					Utils::transfer($sender, "hub");
					return;
				}

				$all = yield WarpHandler::getInstance()->getAllWarps();

				if(isset($all["Spawn"])){
					$all["Spawn"]->teleport($sender);
				} else {
					if(!Utils::isHubServer()){
						Utils::transfer($sender, "hub");
						return;
					}

					$sender->teleport(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
				}
			});
		}
	}
}