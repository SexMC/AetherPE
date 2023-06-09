<?php

namespace skyblock\commands\staff;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\caches\skin\SkinCache;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\LootboxArgument;
use skyblock\commands\arguments\PlayerArgument;
use skyblock\items\lootbox\Lootbox;
use skyblock\items\lootbox\LootboxHandler;
use skyblock\Main;
use skyblock\utils\Utils;

class LootboxCommand extends AetherCommand {

	protected function prepare() : void{
		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new LootboxArgument("lootbox"));
		$this->registerArgument(2, new IntegerArgument("amount", true));
		$this->setPermission("skyblock.command.lootbox");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$lootbox = $args["lootbox"];
		$amount = $args["amount"] ?? 1;
		$player = Server::getInstance()->getPlayerExact($args["player"]);

		if($player === null){
			$sender->sendMessage(Main::PREFIX . "Invalid player");
			return;
		}

		Utils::addItem($player, $lootbox::getItem()->setCount($amount));
		$sender->sendMessage(Main::PREFIX . "gave {$player->getName()} a {$lootbox::getName()}");
	}
}