<?php

declare(strict_types=1);

namespace skyblock\forms\island\management;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Dropdown;
use pocketmine\player\Player;
use skyblock\islands\Island;
use skyblock\utils\IslandUtils;

class IslandKickMemberForm extends CustomForm {

	public function __construct(Island $island, Player $kicker) {
		if($island->isDisbanding()) return;

		parent::__construct("Kick Member", [
			$dropdown = $this->getDropDown($island, $kicker)
		], function (Player $player, CustomFormResponse $response) use ($dropdown, $island): void {
			$kick = $dropdown->getOption($response->getInt("player_dropdown"));
			if($kick === "No members found") return;
			IslandUtils::kickMember($island, $player, $kick);
		});
	}

	public function getDropDown(Island $island, Player $kicker): Dropdown {
		$options = [];

		foreach($island->getMembers() as $member){
			if(strtolower($member) === strtolower($kicker->getName())) continue;

			$options[] = $member;
		}
		if(empty($options)){
			$options[] = "No members found";
		}

		return new Dropdown("player_dropdown", "Select a player", $options);
	}
}