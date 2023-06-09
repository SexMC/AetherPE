<?php

declare(strict_types=1);

namespace skyblock\menus\trades;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\misc\trades\Trade;
use skyblock\misc\trades\TradesHandler;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class TradeMenu extends AetherMenu {

	private array $slots = [
		20 => 1,
		21 => 5,
		22 => 10,
		23 => 32,
		24 => 64,
	];

	public function __construct(private Trade $trade, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;


		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
		}


		foreach($this->slots as $k => $v){
			$menu->getInventory()->setItem($k, $this->trade->getViewItem($v));
		}

		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo back")->setLore(["§r§7To Trades"]));
		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));





		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$player = $transaction->getPlayer();

		if($slot === 49) {
			$player->removeCurrentWindow();
			return;
		}

		if($slot === 48){
			(new TradesViewMenu($player, $this))->send($player);
			return;
		}

		if(isset($this->slots[$slot])){
			$count = $this->slots[$slot];

			$itemNeeded = $this->trade->getInput()->setCount($this->trade->getInput()->getCount() * $count);

			if(!$player->getInventory()->contains($itemNeeded)){
				$player->sendMessage(Main::PREFIX . "§cYou don't have enough items to execute this trade!");
				$player->removeCurrentWindow();
				return;
			}

			$player->getInventory()->removeItem($itemNeeded);
			Utils::addItem($player, $this->trade->getOutput()->setCount($count));
		}
	}
}