<?php

declare(strict_types=1);

namespace skyblock\forms\island;

use Closure;
use developerdino\profanityfilter\Filter;
use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\IslandUtils;

class IslandCreateForm extends CustomForm {

	public function __construct()
	{
		parent::__construct("Create an island", $this->getAll(), Closure::fromCallable([$this, "handle"]), function(Player $player): void {
			$player->sendForm(new IslandForm(new Session($player)));
		});
	}

	public function handle(Player $player, CustomFormResponse $response): void {
		$name = $response->getString("input");

		if($name === ""){
			$player->sendMessage(Main::PREFIX . "§cPlease enter an island name");
			return;
		}

		if(strlen($name) > 20 || strlen($name) < 2){
			$player->sendMessage(Main::PREFIX . "§cIsland name must be between 2 and 20 characters");
			return;
		}

		foreach(str_split($name) as $str){
			if(is_numeric($str) || ctype_alpha($str) || $str == " ") {
				continue;
			}

			$player->sendMessage(Main::PREFIX . "§cIsland name cannot contain special characters");
			return;
		}

		if(Filter::getInstance()->hasProfanity($name)){
			$player->sendMessage(Main::PREFIX . "§cIsland name cannot contain bad words!");
			return;
		}


		if(IslandUtils::islandExists($name)){
			$player->sendMessage(Main::PREFIX . "§b$name §cis already used. Island name must be unique!");
			return;
		}

		IslandUtils::createIsland($player, new Session($player), $name);
	}

	public function getAll(): array {
		return [new Input("input", "Island Name", "Enter the island name (unique)")];
	}
}