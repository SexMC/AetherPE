<?php

declare(strict_types=1);

namespace skyblock\forms\island\management\confirmation;

use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\sessions\Session;

class IslandLeaveConfirmForm extends MenuForm {


	public function __construct(Island $island) {
		if($island->isDisbanding()) return;


		parent::__construct("Island Leave Confirm", "Do you want to leave your current island?", [
			new MenuOption("§aConfirm"),
			new MenuOption("§cExit")
		], function (Player $player, int $button) use ($island): void {
			if($button === 0) {
				$island->announce(Main::PREFIX . "§c{$player->getName()}§7 has left the island");

				$session = new Session($player);
				$session->setIslandName(null);
				$island->removeMember($player->getName());
			}
		});
	}
}