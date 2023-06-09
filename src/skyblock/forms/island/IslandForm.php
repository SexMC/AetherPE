<?php

declare(strict_types=1);

namespace skyblock\forms\island;


use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\forms\island\management\IslandManageForm;
use skyblock\forms\island\top\IslandTopForm;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\utils\IslandUtils;

class IslandForm extends MenuForm {

	public function __construct(private Session $session){

		if($session->getIslandName() !== null){
			$this->islandForm();
		} else {
			$this->noIslandForm();
		}
	}

	public function islandForm(): void {
		parent::__construct("Island Form", "", [
			new MenuOption("Island Home"),
			new MenuOption("Island Manage"),
			new MenuOption("Island Quests"),
			new MenuOption("Island Warps"),
			new MenuOption("Top 10 Islands"),
			new MenuOption("Island Info"),
		], \Closure::fromCallable([$this, "handle"]));

	}

	public function noIslandForm(): void {
		parent::__construct("Island Form", "", [
			new MenuOption("Create Island"),
			new MenuOption("Top 10 Islands"),
			new MenuOption("Island Warps"),
			new MenuOption("Island Invitations")
		], \Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $index): void {
		$session = new Session($player);
		$option = $this->getOption($index);

		switch(strtolower($option->getText())){
			case "island manage":
				$player->sendForm(new IslandManageForm($player, new Island($this->session->getIslandName())));
				break;
			case "create island":
				$player->sendForm(new IslandCreateForm());
				break;
			case "top 10 islands":
				$player->sendForm(new IslandTopForm());
				break;
			case "island warps":
				$player->sendForm(new IslandWarpForm($player));
				break;
			case "island invitations":
				$player->sendForm(new IslandInvitationsForm($session));
				break;
			case "island info":
				$player->sendForm(new IslandInfoForm(new Island($this->session->getIslandName())));
				break;
			case "island home":
				IslandUtils::go($player, $session);
				break;
			case "island quests":
				$player->sendMessage(Main::PREFIX . "This feature is locked down by warpspeed (/warpspeed)");
				break;
		}
	}

}