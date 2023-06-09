<?php

declare(strict_types=1);

namespace skyblock\forms\island\top;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

class IslandTopViewForm extends MenuForm {
	public function __construct(string $title, string $text){
		parent::__construct($title, $text, [new MenuOption("<- Back>")], Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $btn): void {
		if($btn === 0){
			$player->sendForm(new IslandTopForm());
		}
	}
}