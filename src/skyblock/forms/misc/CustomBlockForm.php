<?php

declare(strict_types=1);

namespace skyblock\forms\misc;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use skyblock\blocks\custom\CustomBlockTile;
use skyblock\menus\AetherMenu;
use skyblock\utils\TimeUtils;

class CustomBlockForm extends MenuForm {

	public function __construct(private CustomBlockTile $tile){
		parent::__construct($this->tile->getSpecialBlock()::getItem()->getCustomName(), $this->getText(), $this->getButtons(), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $btn): void {
		if($this->tile->isClosed()) return;
		$button = $this->getOption($btn);

		if($button->getText() === "§aClaim Rewards"){
			if(!$this->tile->getSpecialBlock()->hasAvailableRewards($this->tile)) return;

			$this->tile->getSpecialBlock()->giveRewards($player, $this->tile);
		}
	}

	public function getButtons(): array {
		$arr = [];

		if($this->tile->getSpecialBlock()->hasAvailableRewards($this->tile)){
			$arr[] = new MenuOption("§aClaim Rewards");
		}

		$arr[] = new MenuOption("§cClose");

		return $arr;
	}

	public function getText(): string {
		$arr = [
			"§r§7Block: §c" . $this->tile->getSpecialBlock()::getItem()->getCustomName(),
			"§r§7Placed by: §c" . $this->tile->getPlacer(),
			"§r§7Age: §c" . TimeUtils::getFormattedTime(time() - $this->tile->getTimePlaced()),
			"§r§7Rewards: " . ($this->tile->getSpecialBlock()->hasAvailableRewards($this->tile) ? "§aAvailable" : "§cNot available")
		];


		return implode("\n", $arr);
	}
}