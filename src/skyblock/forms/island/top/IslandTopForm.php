<?php

declare(strict_types=1);

namespace skyblock\forms\island\top;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\communication\operations\economy\TopOperation;
use skyblock\forms\island\IslandForm;
use skyblock\Main;
use skyblock\sessions\Session;
use SOFe\AwaitGenerator\Await;

class IslandTopForm extends MenuForm {

	public function __construct(){
		parent::__construct("Island Top", "Select an option", [new MenuOption("§bValue"), new MenuOption("§4Power"), new MenuOption("<- Back")], Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button): void {
		if($button === 0){
			Await::f2c(function() use($player){
				Main::getInstance()->getCommunicationLogicHandler()->addOperation(new TopOperation(TopOperation::TYPE_ISLAND_VALUE, 0, 9, yield Await::RESOLVE));
				$data = yield Await::ONCE;


				if(isset($data["message"])){
					$text = "";
					$num = 1;
					foreach($data["message"] as $d){
						$money = $d["Value"];
						$text .= "\n" . "§c#$num §7{$d["Player"]} - §c$" . number_format($money);

						$num++;
					}

					$player->sendForm(new IslandTopViewForm("Island Top Value", $text));
				}
				});

		} elseif($button === 1){
			Await::f2c(function() use($player){
				Main::getInstance()->getCommunicationLogicHandler()->addOperation(new TopOperation(TopOperation::TYPE_ISLAND_POWER, 0, 9, yield Await::RESOLVE));
				$data = yield Await::ONCE;


				if(isset($data["message"])){
					$text = "";
					$num = 1;
					foreach($data["message"] as $d){
						$money = $d["Value"];
						$text .= "\n" . "§c#$num §7{$d["Player"]} - §c" . number_format($money);

						$num++;
					}

					$player->sendForm(new IslandTopViewForm("Island Top Power", $text));

				}
			});
		} else {
			$player->sendForm(new IslandForm(new Session($player)));
		}
	}
}