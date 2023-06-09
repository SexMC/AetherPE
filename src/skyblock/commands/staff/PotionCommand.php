<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\Server;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\AetherPotionArgument;
use skyblock\items\potions\AetherPotion;
use skyblock\items\potions\SkyBlockPotion;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\utils\Utils;

class PotionCommand extends AetherCommand {
	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new AetherPotionArgument("type"));
		$this->registerArgument(2, new IntegerArgument("level"));
		$this->registerArgument(3, new IntegerArgument("durationInSeconds"));

		$this->setPermission("skyblock.command.potion");
	}

	public function onPlayerRun(AetherPlayer $player, string $aliasUsed, array $args) : void{
		$p = Server::getInstance()->getPlayerByPrefix($args["player"]);

		if($p === null){
			$player->sendMessage(Main::PREFIX . "Invalid player");
			return;
		}
		/** @var SkyBlockPotion $potion */
		$potion = clone $args["type"];
		$potion->setPotionLevel($args["level"]);
		$potion->setDuration($args["durationInSeconds"]);

		$player->sendMessage(Main::PREFIX . "Gave {$p->getName()} " . $potion->getCustomName());
		Utils::addItem($p, $potion);
	}
}