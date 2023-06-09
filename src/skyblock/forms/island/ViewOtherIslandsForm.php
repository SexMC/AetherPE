<?php

declare(strict_types=1);

namespace skyblock\forms\island;

use Closure;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\Utils;


class ViewOtherIslandsForm extends CustomForm {

	public function __construct() {
		parent::__construct(
			"View other islands",
			[new Input("input", "Enter a player name")],
			Closure::fromCallable([$this, "handle"])
		);
	}

	public function handle(Player $player, CustomFormResponse $response): void {
		$string = $response->getString("input");
		if($string === "") {
			$player->sendMessage(Main::PREFIX . "§7Please enter something");
			return;
		}

		$island = null;
		if(($is = (new Island($string)))->exists()){
			$island = $is;
		}

		if(Utils::isOnline($string )&& $island === null){
			$session = new Session($string);
			if(($n = $session->getIslandName()) !== null){
				$island = new Island($n);
			}
		}

		if($island instanceof Island){
			$player->sendForm(new IslandInfoForm($island));
		} else $player->sendMessage(Main::PREFIX . "§c$string §7is not in an island");
	}
}