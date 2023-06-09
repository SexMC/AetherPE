<?php

declare(strict_types=1);

namespace skyblock\forms\island\admin;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\forms\island\management\confirmation\IslandDisbandConfirmForm;
use skyblock\forms\island\management\IslandAddMemberForm;
use skyblock\forms\island\management\IslandKickMemberForm;
use skyblock\forms\island\management\IslandSettingsForm;
use skyblock\forms\island\management\permissions\IslandPermissionsForm;
use skyblock\forms\island\unlockables\IslandUnlockablesForm;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\menus\island\IslandUpgradeMenu;
use skyblock\sessions\Session;
use skyblock\utils\IslandUtils;
use skyblock\utils\Utils;

class IslandAdminForm extends MenuForm {

	public function __construct(private Island $island){
		parent::__construct("Island Admin", "", $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}
	
	public function handle(Player $player, int $btn): void {
		switch($this->getOption($btn)->getText()){
			case "Teleport":
				IslandUtils::go($player, (new Session($player)), $this->island->getName());
				break;
			case "Kick":
				$player->sendForm(new IslandKickMemberForm($this->island, $player));
				break;
			case "Invite":
				$player->sendForm(new IslandAddMemberForm($this->island));
				break;
			case "Permissions":
				$player->sendForm(new IslandPermissionsForm($player, $this->island));
				break;
			case "Settings":
				$player->sendForm(new IslandSettingsForm($this->island));
				break;
			case "Update Home":
				if($player->getLocation()->getWorld()->getFolderName() === $this->island->getWorldName()){
					$this->island->setHome(($v = $player->getLocation()));
					$this->island->announce(Main::PREFIX . "{$player->getName()} has updated the island home location to x: {$v->getFloorX()} y: {$v->getFloorY()} z: {$v->getFloorZ()}");
				} else $player->sendMessage(Main::PREFIX . "§cYou can only set island home on the island!");
				break;
			case "Update Warp":
				if($player->getLocation()->getWorld()->getFolderName() === $this->island->getWorldName()){
					$this->island->setWarp($v = $player->getLocation()->asVector3());
					$this->island->announce(Main::PREFIX . "{$player->getName()} has updated the island warp location to x: {$v->getFloorX()} y: {$v->getFloorY()} z: {$v->getFloorZ()}");
				} else $player->sendMessage(Main::PREFIX . "§cYou can only set island warps on the island!");
				break;
			case "Upgrades":
				(new IslandUpgradeMenu($this->island))->send($player);
				break;
			case "Unlockables":
				$player->sendForm(new IslandUnlockablesForm());
				break;
			case "Disband":
				$player->sendForm(new IslandDisbandConfirmForm($this->island));
				break;
			case "Values":
				$player->sendForm(new IslandValuesForm($this->island));
				break;
		}
	}
	
	public function getButtons(): array {
		return [
			new MenuOption("Teleport"),
			new MenuOption("Values"),
			new MenuOption("Kick"),
			new MenuOption("Invite"),
			new MenuOption("Permissions"),
			new MenuOption("Settings"),
			new MenuOption("Update Home"),
			new MenuOption("Update Warp"),
			new MenuOption("Upgrades"),
			new MenuOption("Unlockables"),
			new MenuOption("Disband"),
		];
	}
}