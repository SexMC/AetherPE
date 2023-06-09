<?php

declare(strict_types=1);

namespace skyblock\menus\items;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;
use skyblock\player\AetherPlayer;

class AccessoryBagMenu extends AetherMenu {

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$menu->setName("Accessories");


		$defaultSlots = 3;
		$extra = $this->player->getAccessoryData()->getExtraAccessorySlots();
		$all = $this->player->getAccessoryData()->getAccessories();



		$barrier = VanillaBlocks::IRON_BARS()->asItem();
		$barrier->setCustomName("§r§cLocked slot");
		$barrier->setLore(["§r§7More accessory slots can be unlocked", "§r§7with a §bhigher rank§7 or the §credstone§7 collection."]);
		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, $barrier);
		}

		$menu->getInventory()->setItem(0, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock menu"]));
		$menu->getInventory()->setItem(1, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§aTake out accessories")->setLore(["§r§7Click to take out accessories."]));


		for($i = 2; $i < ($defaultSlots + $extra) + 2; $i++){
			$menu->getInventory()->setItem($i, VanillaItems::AIR());
		}

		foreach($all as $k => $accessory){
			$menu->getInventory()->addItem($accessory);
		}





		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();
		assert($player instanceof AetherPlayer);


		if($slot === 0){
			(new SkyblockMenu($player, $this))->send($player);
			return;
		}

		if($slot === 1){
			(new AccessoryBagTakeMenu($player, $this))->send($player);
			return;
		}
	}
}