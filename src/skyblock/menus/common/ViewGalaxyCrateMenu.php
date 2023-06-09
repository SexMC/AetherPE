<?php

declare(strict_types=1);

namespace skyblock\menus\common;

use muqsit\invmenu\InvMenu;
use skyblock\items\crates\Crate;
use skyblock\items\crates\CrateItem;
use skyblock\menus\AetherMenu;

class ViewGalaxyCrateMenu extends AetherMenu {

	public function __construct(private Crate $crate){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName($this->crate->getKeyItem(1)->getCustomName());

		/** @var CrateItem $reward */
		foreach($this->crate->getUniqueRewards() as $reward){
			$i = clone $reward->getItem();

			if($reward->getMinCount() !== $reward->getMaxCount()){
				$lore = $i->getLore();
				$lore[] = "§r";
				$lore[] = "§r§bCount: §d{$reward->getMinCount()}x-{$reward->getMaxCount()}x";
				$i->setLore($lore);
			} else {
				$lore = $i->getLore();
				$lore[] = "§r";
				$lore[] = "§r§bCount: §d{$reward->getItem()->getCount()}x";
				$i->setLore($lore);
			}

			$menu->getInventory()->addItem($i);
		}


		return $menu;
	}
}