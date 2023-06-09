<?php

declare(strict_types=1);

namespace skyblock\forms\commands;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\Main;
use skyblock\misc\warps\Warp;

class WarpForm extends MenuForm {

	public function __construct(private array $warps){
		parent::__construct("Warps", "Select a warp", array_map(fn(Warp $warp) => new MenuOption($warp->name . "\n" . ($warp->open === true ? "§aOpen" : "§cClosed")), $this->warps), Closure::fromCallable([$this, "handle"]));
	}

	public function handle(Player $player, int $button): void {
		$warp = $this->warps[TextFormat::clean(explode("\n", $this->getOption($button)->getText())[0])];

		if($warp instanceof Warp){
			$player->sendMessage(Main::PREFIX . "Teleporting to warp " . $warp->name);
			$warp->teleport($player);
		}
	}
}