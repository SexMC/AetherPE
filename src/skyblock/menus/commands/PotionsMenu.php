<?php

declare(strict_types=1);

namespace skyblock\menus\commands;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;

class PotionsMenu extends AetherMenu {

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$menu->setName("Potions");


		$i = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");
		foreach(array_merge(range(0, 17), [18, 27, 36, 45, 26, 35, 44, 53]) as $slot){
			$menu->getInventory()->setItem($slot, $i);
		}

		$menu->getInventory()->setItem(4, $this->getInfoItem());

		foreach($this->player->getPotionData()->getActivePotions() as $potion){
			$i = clone $potion->item;
			$i->setDuration($potion->leftDuration);

			$menu->getInventory()->addItem($i);
		}
		$menu->getInventory()->setItem(0, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock menu"]));


		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();

		if($slot === 0){
			(new SkyblockMenu($this->player, $this))->send($this->player);
		}
	}

	public function getInfoItem(): Item {
		return VanillaItems::GLASS_BOTTLE()
			->setCustomName("§r§b§lActive Potions")
			->setLore(["§r§7Your active potions are listed below"]);
	}
}