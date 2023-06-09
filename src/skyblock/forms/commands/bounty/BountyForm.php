<?php

declare(strict_types=1);

namespace skyblock\forms\commands\bounty;

use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\forms\AetherMenuForm;
use skyblock\misc\bounty\BountyData;
use skyblock\misc\bounty\BountyHandler;
use SOFe\AwaitGenerator\Await;

class BountyForm extends AetherMenuForm {

	public function __construct(private array $data){
		parent::__construct(
			"Bounties",
			"",
			array_map(fn(array $data) => new MenuOption($data["username"] . "\n§c$" . number_format($data["currentBounty"])), $this->data)
		);
	}

	public function onHandle(Player $player, MenuOption $button, int $index) : void{
		$username = explode("\n", $button->getText())[0];

		Await::f2c(function() use($player, $username) {
			$player->sendForm(new BountyInfoForm(yield BountyHandler::getInstance()->getBountyData($username)));
		});
	}
}