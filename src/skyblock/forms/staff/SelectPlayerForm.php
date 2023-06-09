<?php

declare(strict_types=1);

namespace skyblock\forms\staff;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\utils\Utils;

class SelectPlayerForm extends MenuForm {

	public function __construct(){
		$sorted = Utils::getOnlinePlayerUsernames();
		asort($sorted);

		parent::__construct("Select a player", "", array_map(fn(string $s) => new MenuOption($s), $sorted), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $btn): void {
		$player->sendForm(new PlayerInfoForm($this->getOption($btn)->getText()));
	}
}