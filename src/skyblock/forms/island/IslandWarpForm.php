<?php

declare(strict_types=1);

namespace skyblock\forms\island;

use dktapps\pmforms\CustomForm;
use dktapps\pmforms\CustomFormResponse;
use dktapps\pmforms\element\Input;
use pocketmine\player\Player;
use skyblock\sessions\Session;
use skyblock\utils\IslandUtils;

class IslandWarpForm extends CustomForm {

	public function __construct(Player $player) {
		$default = "";
		$session = new Session($player);
			if($s = $session->getIslandName()) {
				$default = $s;
			}

		parent::__construct("Island Warp", [
			new Input("input", "Enter a player name", "Player name", $default)
		], function (Player $player, CustomFormResponse $response): void {
			$string = $response->getString("input");
			if($string === "") return;
			IslandUtils::teleportWarp($player, $string);
		});
	}
}