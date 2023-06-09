<?php

declare(strict_types=1);

namespace skyblock\menus\items;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\IntTag;
use skyblock\items\misc\storagesack\StorageSack;
use skyblock\menus\AetherMenu;
use skyblock\utils\Utils;

class StorageSackMenu extends AetherMenu {

	public function __construct(private StorageSack $item){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$this->menu = $menu;

		$menu->setName("§r" . $this->item->getCustomName());
		$this->updateMenu();

		return $menu;
	}

	public function updateMenu(): void {
		$menu = $this->menu;

		$menu->getInventory()->clearAll();
		$capacity = $this->item->getCapacity();
		foreach($this->item->getNamedTag()->getCompoundTag("storage_items")->getValue() as $k => $tag){
			if($tag instanceof IntTag){
				$split = explode(":", $k);
				$item = ItemFactory::getInstance()->get((int) $split[0], (int) $split[1]);

				$item->setCustomName("§r§a" . $item->getName());
				$item->setLore([
					"§r§7Stored: §c" . number_format($tag->getValue()) . "§7/§b" . number_format($capacity),
					"§r",
					"§r§eClick to pickup!",
				]);

				$menu->getInventory()->addItem($item);
			}
		}

		foreach($menu->getInventory()->getContents(true) as $k => $v){
			if($v->isNull()){
				$menu->getInventory()->setItem($k, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
			}
		}
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$out = $transaction->getOut();

		if(!$player->getInventory()->getItemInHand()->equals($this->item)){
			$player->removeCurrentWindow();;
			return;
		}

		$compound = $this->item->getNamedTag()->getCompoundTag("storage_items");
		$key = $out->getId() . ":" . $out->getMeta();
		if(($int = $compound->getInt($key, -1)) > 0){
			$item = ItemFactory::getInstance()->get($out->getId(), $out->getMeta())->setCount($int);
			$count = $player->getInventory()->getAddableItemQuantity($item);

			$new = $int - $count;
			if($new < 0){
				$player->removeCurrentWindow();
				return;
			}


			$compound->setInt($key, $new);
			$player->getInventory()->setItemInHand($this->item);
			Utils::addItem($player, $item->setCount($count), true, false);

			$this->updateMenu();
		}
	}
}