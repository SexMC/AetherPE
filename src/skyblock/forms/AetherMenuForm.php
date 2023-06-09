<?php

declare(strict_types=1);

namespace skyblock\forms;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;

abstract class AetherMenuForm extends MenuForm {
	public function __construct(string $title, string $text, array $options) {
		parent::__construct($title, $text, $options, Closure::fromCallable([$this, "handle"]), Closure::fromCallable([$this, "onClose"]));
	}

	private function handle(Player $player, int $index) : void {
		$this->onHandle($player, $this->getOption($index), $index);
	}

	public function onHandle(Player $player, MenuOption $button, int $index) : void {}

	public function onClose(Player $player) : void {}
}