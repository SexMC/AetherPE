<?php

declare(strict_types=1);

namespace skyblock\menus\items;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\item\VanillaItems;
use skyblock\items\accessory\Accessory;
use skyblock\items\accessory\AccessoryItem;
use skyblock\menus\AetherMenu;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class AccessoryBagTakeMenu extends AetherMenu {

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$menu->setName("Accessories Take");



		$all = $this->player->getAccessoryData()->getAccessories();



		$menu->getInventory()->setItem(0, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To accessory menu"]));


		foreach($all as $accessory){
			$menu->getInventory()->addItem($accessory);
		}



		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$item = $transaction->getItemClicked();
		$slot = $transaction->getAction()->getSlot();

		assert($player instanceof AetherPlayer);

		if($slot === 0){
			(new AccessoryBagMenu($player, $this))->send($player);
			return;
		}

		if(!$item instanceof AccessoryItem) return;

		$id = $item->getAccessoryName();


		$all = $player->getAccessoryData()->getAccessories();

		if(!isset($all[$id])) return;
		$accessory = $all[$id];

		unset($all[$id]);
		$player->getAccessoryData()->setAccessories($all);

		$this->getMenu()->getInventory()->setItem($slot, VanillaItems::AIR());
		Utils::addItem($player, $accessory);
	}
}