<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\ItemEditor;
use skyblock\items\itemmods\ItemMod;
use skyblock\Main;
use skyblock\utils\Utils;

class RemoveItemModCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("Remove item mods from your items");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$item = $sender->getInventory()->getItemInHand();
			$mods = ItemEditor::getItemMods($item);

			if(empty($mods)){
				$sender->sendMessage(Main::PREFIX . "This item does not have any item mods applied");
				return;
			}

			$selected = $mods[array_rand($mods)];
			ItemEditor::removeItemMod($item, $selected);
			$sender->getInventory()->setItemInHand($item);
			Utils::addItem($sender, ItemMod::getItem($selected));

			$sender->sendMessage(Main::PREFIX . "Removed the {$selected} item mod");
		}
	}
}