<?php

declare(strict_types=1);

namespace skyblock\commands\staff;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\commands\arguments\ItemModArgument;
use skyblock\items\itemmods\ItemMod;
use skyblock\items\itemmods\ItemModHandler;
use skyblock\items\itemskins\ItemSkin;
use skyblock\items\itemskins\ItemSkinHandler;
use skyblock\Main;

class ItemModCommand extends AetherCommand {

	public function __construct(string $name, array $aliases = [])
	{
		parent::__construct($name, $aliases);

		$this->setPermission("skyblock.command.itemmod");
	}


	public function onExecute(CommandSender $sender, string $commandLabel, array $args): void
	{

		if(isset($args[0]) && isset($args[1])){
			$p = $args[0];
			if($p = $sender->getServer()->getPlayerExact($p)){
				$skin = ItemModHandler::getInstance()->getItemMod($args[1]);
				if($skin instanceof ItemMod){
					$p->getInventory()->addItem($skin::getItem($skin->getUniqueID()));
					$sender->sendMessage(Main::PREFIX . "Gave {$p->getName()} a {$skin->getUniqueID()} itemmod");
				} else $sender->sendMessage(Main::PREFIX . "Invalid item mod");
			} else $sender->sendMessage(Main::PREFIX . "Invalid player");
		}
	}

	protected function prepare() : void{
		$this->registerArgument(0, new ItemModArgument("itemmod"));
		
		$this->setPermission("skyblock.command.itemmod");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			/** @var ItemMod $mod */
			$mod = $args["itemmod"];
			$sender->getInventory()->addItem($mod::getItem($mod->getUniqueId()));
			$sender->sendMessage(Main::PREFIX . "Gave {$sender->getName()} a {$mod->getUniqueID()} itemmod");
		}
	}
}