<?php

declare(strict_types=1);

namespace skyblock\forms\island\management;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use RedisClient\Pipeline\PipelineInterface;
use RedisClient\Pipeline\Version\Pipeline6x0;
use skyblock\Database;
use skyblock\forms\island\IslandForm;
use skyblock\forms\island\management\confirmation\IslandLeaveConfirmForm;
use skyblock\forms\island\management\IslandAddMemberForm;
use skyblock\forms\island\management\confirmation\IslandDisbandConfirmForm;
use skyblock\forms\island\management\IslandKickMemberForm;
use skyblock\forms\island\management\permissions\IslandPermissionsForm;
use skyblock\forms\island\unlockables\IslandUnlockablesForm;
use skyblock\islands\Island;
use skyblock\islands\IslandInterface;
use skyblock\Main;
use skyblock\menus\island\IslandUpgradeMenu;
use skyblock\sessions\Session;

class IslandManageForm extends MenuForm{

	private array $results = [];

	public function __construct(private Player $player, private Island $island, private bool $admin = false){

		if($this->island->isDisbanding()) return;

		$isLeader = $island->isLeader($player);
		$result = Database::getInstance()->getRedis()->pipeline(function(PipelineInterface $pipeline) use ($player, $island){
			$name = strtolower($this->player->getName());

			/** @var Pipeline6x0 $pipeline */
			foreach(IslandInterface::MANAGE_PERMISSIONS as $permission){
				$pipeline->get("island.{$island->getName()}.perms.{$name}.$permission");
				}
			}
		);

		foreach($result as $k => $v){
			unset($result[$k]);
			if($v === null){
				$v = false;
			}

			$result[IslandInterface::MANAGE_PERMISSIONS[$k]] = (bool) (($isLeader === true || $this->admin === true) ? true : $v);
		}

		$this->results = $result;

		parent::__construct("Manage Island ({$this->island->getName()})", "", $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button) : void{
		$clicked = str_replace(" ", "_", strtolower($this->getOption($button)->getText()));

		switch($clicked){
			case IslandInterface::PERMISSION_SET_ISLAND_WARP:
				if($player->getLocation()->getWorld()->getFolderName() === $this->island->getWorldName()){
					$this->island->setWarp($v = $player->getLocation()->asVector3());
					$this->island->announce(Main::PREFIX . "{$player->getName()} has updated the island warp location to x: {$v->getFloorX()} y: {$v->getFloorY()} z: {$v->getFloorZ()}");
				} else $player->sendMessage(Main::PREFIX . "§cYou can only set island warps on your own island!");
				break;
			case IslandInterface::PERMISSION_SET_ISLAND_HOME:
				if($player->getLocation()->getWorld()->getFolderName() === $this->island->getWorldName()){
					$this->island->setHome(($v = $player->getLocation()));
					$this->island->announce(Main::PREFIX . "{$player->getName()} has updated the island home location to x: {$v->getFloorX()} y: {$v->getFloorY()} z: {$v->getFloorZ()}");
				} else $player->sendMessage(Main::PREFIX . "§cYou can only set island home on your own island!");				break;
			case IslandInterface::PERMISSION_EDIT_SETTINGS:
				$player->sendForm(new IslandSettingsForm($this->island));
				break;
			case IslandInterface::PERMISSION_INVITE_PLAYERS:
				$player->sendForm(new IslandAddMemberForm($this->island));
				break;
			case IslandInterface::PERMISSION_KICK_MEMBERS:
				$player->sendForm(new IslandKickMemberForm($this->island, $player));
				break;
			case IslandInterface::PERMISSION_EDIT_MEMBER_PERMISSIONS:
				$player->sendForm(new IslandPermissionsForm($player, $this->island));
				break;
			case "unlockables":
				$player->sendForm(new IslandUnlockablesForm());
				break;
			case "disband":
				$player->sendForm(new IslandDisbandConfirmForm($this->island));
				break;
			case "leave_island":
				$player->sendForm(new IslandLeaveConfirmForm($this->island));
				break;
			case "<-_back":
				$player->sendForm(new IslandForm(new Session($player)));
				break;
			case "upgrades":
				(new IslandUpgradeMenu($this->island))->send($player);
				break;

		}
	}


	public function getButtons() : array{
		$array = [new MenuOption("Unlockables"), new MenuOption("Upgrades")];

		foreach(IslandInterface::MANAGE_PERMISSIONS as $perm) {
			if(isset($this->results[$perm]) && $this->results[$perm] === true){
				$array[] = new MenuOption(ucwords(str_replace("_", " ", $perm)));
			}
		}

		if($this->island->isLeader($this->player) || $this->admin === true){
			$array[] = new MenuOption("Disband");
		} else $array[] = new MenuOption("Leave Island");

		$array[] = new MenuOption("<- Back");


		return $array;
	}
}