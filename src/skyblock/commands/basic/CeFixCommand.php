<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\item\Armor;
use pocketmine\item\Tool;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\ItemEditor;
use skyblock\items\SkyblockItem;
use skyblock\Main;

class CeFixCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("Fix glitched items");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;

		$item = $sender->getInventory()->getItemInHand();

		if($item instanceof SkyblockItem){
			$item->resetLore();
			$sender->getInventory()->setItemInHand($item);

			return;
		}

		if($item instanceof SkyblockItem){
			/** @var CustomEnchantInstance[] $ce */


			if($item instanceof SkyblockItem){
				$item->resetLore();
			}
			$sender->getInventory()->setItemInHand($item);
			$sender->sendMessage(Main::PREFIX . "Updated the item in your hand");
		} else $sender->sendMessage(Main::PREFIX . "This item doesn't contain custom enchants");
	}
}