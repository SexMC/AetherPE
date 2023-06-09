<?php

declare(strict_types=1);

namespace skyblock\forms\island\management;


use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\player\Player;
use RedisClient\Pipeline\PipelineInterface;
use RedisClient\Pipeline\Version\Pipeline6x0;
use skyblock\communication\operations\server\ServerMessageOperation;
use skyblock\Database;
use skyblock\islands\Island;
use skyblock\islands\IslandInterface;
use skyblock\islands\upgrades\types\IslandMemberUpgrade;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class IslandAddMemberForm extends CustomForm {

	public function __construct(Island $island) {
		if($island->isDisbanding()) return;


		$options = $this->getPlayersWithNoIsland();
		parent::__construct("Add Member", [
			new Dropdown("player_dropdown", "Select a player", $options)
		], function (Player $player, CustomFormResponse $response) use ($island, $options): void {
			$am = IslandInterface::MEMBER_LIMIT;
			if(($lvl = $island->getIslandUpgrade(IslandMemberUpgrade::getIdentifier())) > 0){
				$am += $lvl;
			}

			if(count($island->getMembers()) >= $am){
				$player->sendMessage("§7You already have §c$am §7members");
				return;
			}
			$invite = $options[$response->getInt("player_dropdown")];
			if($invite === "No players found") return;
			$session = new Session($invite);
			
			if(Utils::isOnline($invite)){
				if($session->getIslandName() === null){
					$session->addInvitation($island->getName());
					$island->announce(Main::PREFIX . "§c{$player->getName()}§7 has invited §c{$session->getUsername()}§7 to the island.");
					Utils::sendMessage($session->getUsername(), Main::PREFIX . "You have been invited to §c{$island->getName()} §7by §c{$player->getName()}.");
				} else $player->sendMessage(Main::PREFIX . "this player is already in an island!");
			} else $player->sendMessage(Main::PREFIX . "§c$invite §7is not online anymore.");
		});
	}

	public function getPlayersWithNoIsland(): array {
		$array = [];


		$online = Utils::getOnlinePlayerUsernames();
		$result = Database::getInstance()->getRedis()->pipeline(function(PipelineInterface $pipeline) use ($online){
				foreach($online as $username){
					$pipeline->get("player.{$username}.islandName");
				}
			}
		);
		//array_pop($result);

		foreach($result as $k => $v){
			if($v === "" || $v === null){
				$username = $online[$k];
				$array[] = $username;
			}
		}


		if($array === []) {
			$array[] = "No players found";
		}

		return $array;
	}
}