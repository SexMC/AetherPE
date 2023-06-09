<?php

declare(strict_types=1);

namespace skyblock\forms\island\management\permissions;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\forms\island\management\IslandManageForm;
use skyblock\islands\Island;


class IslandPermissionsForm extends MenuForm {


	public function __construct(private Player $player, private Island $island) {
		if($island->isDisbanding()) return;


		parent::__construct("Permissions Manager",
			"Select a member",
			array_merge($this->getAll(), [new MenuOption("<-- Back")]),
			Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button): void {
		$clicked = $this->getOption($button)->getText();
		if($clicked === "<-- Back"){
			$player->sendForm(new IslandManageForm($player, $this->island));
			return;
		}

		$player->sendForm(new IslandEditPermissionsForm($this->island, $clicked));
	}

	public function getAll(): array {
		$arr = [];

		foreach($this->island->getMembers() as $member){
			if(strtolower($member) === strtolower($this->player->getName())) continue;

			$arr[] = new MenuOption($member);
		}

		return $arr;
	}
}