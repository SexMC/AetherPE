<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\ItemEditor;
use skyblock\Main;
use skyblock\utils\Utils;

class RemoveMaskCommand extends AetherCommand {

	protected function prepare() : void{
		$this->setDescription("Remove masks from your items");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if($sender instanceof Player){
			$item = $sender->getInventory()->getItemInHand();
			$mask = ItemEditor::getMask($item);

			if(!$item instanceof Armor) return;

			if($mask === null){
				$sender->sendMessage(Main::PREFIX . "This item does not have any masks applied");
				return;
			}

			ItemEditor::setMask($item, null);
			$sender->getInventory()->setItemInHand($item);
			Utils::addItem($sender, $mask::getItem());

			$sender->sendMessage(Main::PREFIX . "Removed the §c{$mask::getName()} §7mask");
		}
	}
}