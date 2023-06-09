<?php

declare(strict_types=1);

namespace skyblock\forms\island\management\confirmation;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\islands\Island;
use skyblock\islands\IslandHandler;
use skyblock\Main;
use skyblock\tasks\FileDeleteTask;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class IslandDisbandConfirmForm extends MenuForm {


	public function __construct(Island $island) {
		if($island->isDisbanding()) return;


		parent::__construct("Island Disband Confirm", "Do you want to disband your island?", [
			new MenuOption("§aConfirm"),
			new MenuOption("§cExit")
		], function (Player $player, int $button) use ($island): void {
			if($button === 0) {
				if($player->getWorld()->getDisplayName() !== $island->getWorldName()){
					$player->sendMessage(Main::PREFIX . "You can only disband the island while standing on it");
					return;
				}

				$created = $island->getRedis()->get("island.{$island->getName()}.created") ?? time() - 10 * 60;
				if(time() - $created <= 180){
					$player->sendMessage(Main::PREFIX . "You need to wait " . (180 - (time() - $created)) . " seconds to disband the island");
					return;
				}

				foreach($player->getWorld()->getPlayers() as $p){
					Utils::hub($p);
				}

				$world = $player->getWorld();
				Server::getInstance()->getWorldManager()->unloadWorld($world);


				$island->disband(true);
			}
		});
	}
}