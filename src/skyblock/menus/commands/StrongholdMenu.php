<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use pocketmine\block\VanillaBlocks;
use skyblock\menus\AetherMenu;

class StrongholdMenu extends AetherMenu {
	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_HOPPER);
		$menu->setName("Strongholds");

		$item = VanillaBlocks::CHEST()->asItem();
		$item->setCustomName('§r§l§fStronghold: §l§3Bildge§bwater');
		$item->setLore([
			"§r§7§oThe closer you get, the more scared you get.",
			"§r§7§oYou feel something crawling, might just be a fly, but",
			"§r§7§oare you sure? did you check?",
			"§r",
			"§r§l§8* §r§7This outpost is located in Warzone at 457x, 78y, 457z",
			"§r§7Stand inside the red line to start capturing the outpost",
			"§r§l§8* §r§7Once fully captured, your island gains §l§c+10 §r§cIsland Power",
			"§r§7every 5 minutes.",
			"§r§l§8* §r§7A visitor appears every 15 minutes of being capped",
			"§r§7the visitor does nothing but tries to reset the cap, You can",
			"§r§7kill him to prevent him from stealing the cap.",
			"§r§l§8* §r§7The Outpost chest spawns every hour. Containing",
			"§r§7good loot to who ever does the last blow.",
		]);
		$menu->getInventory()->addItem($item);
		foreach($menu->getInventory()->getContents(true) as $k => $i){
			if($i->isNull()){
				$menu->getInventory()->setItem($k, VanillaBlocks::IRON_BARS()->asItem()->setCustomName(" "));
			}
		}


		return $menu;
	}
}