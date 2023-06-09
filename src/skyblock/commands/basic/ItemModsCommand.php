<?php

declare(strict_types=1);

namespace skyblock\commands\basic;

use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skyblock\commands\AetherCommand;
use skyblock\items\itemmods\ItemMod;
use skyblock\items\itemmods\ItemModHandler;
use skyblock\items\masks\Mask;
use skyblock\items\masks\MasksHandler;
use skyblock\menus\common\ViewPagedItemsMenu;

class ItemModsCommand extends AetherCommand {
	protected function prepare() : void{
		$this->setDescription("View all item mods");
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
		if(!$sender instanceof Player) return;


		((new ViewPagedItemsMenu("Item Mods", array_map(fn(ItemMod $mask) => $mask::getItem($mask::getUniqueID()), ItemModHandler::getInstance()->getAllItemMods()))))->send($sender);
	}
}