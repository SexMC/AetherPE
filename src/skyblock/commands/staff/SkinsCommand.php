<?php

namespace skyblock\commands\staff;

use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\entity\Skin;
use pocketmine\player\Player;
use pocketmine\Server;
use ReflectionClass;
use skyblock\caches\skin\SkinCache;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\PlayerArgument;
use skyblock\commands\arguments\SkinsArgument;
use skyblock\items\itemskins\ItemSkin;
use skyblock\items\itemskins\ItemSkinHandler;
use skyblock\items\special\SpecialItemHandler;
use skyblock\Main;

class SkinsCommand extends AetherCommand {
	public function __construct(string $name, array $aliases = []){
		parent::__construct($name, $aliases);

		$this->setPermission("skyblock.command.skins");
	}

	protected function prepare() : void{
		$this->setPermission("skyblock.command.skins");

		$this->registerArgument(0, new RawStringArgument("player"));
		$this->registerArgument(1, new SkinsArgument("skin"));
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		$player = Server::getInstance()->getPlayerExact($args["player"]);

		if($player === null){
			$sender->sendMessage(Main::PREFIX . "Invalid player");
			return;
		}

		/** @var Skin $skin */
		$skin = $args["skin"];

		$player->setSkin($skin);
		$player->sendSkin();
		$sender->sendMessage(Main::PREFIX . "set {$player->getName()} a {$skin->getSkinId()} skin");
	}
}