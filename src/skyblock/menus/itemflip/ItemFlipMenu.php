<?php

declare(strict_types=1);

namespace skyblock\menus\itemflip;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\data\bedrock\DyeColorIdMap;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\menus\AetherMenu;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

class ItemFlipMenu extends AetherMenu {
	use AwaitStdTrait;

	protected $type = self::NORMAL;

	private array $emptySlots = [
		13,
		22,
		31,
		36, 37, 38, 39, 40, 41, 42, 43, 44, 45,
	];

	private array $player1Slots = [
		1, 2, 3,
		9, 10, 11, 12,
		18, 19, 20, 21,
		27, 28, 29, 30,
	];

	private array $player2Slots = [
		5, 6, 7,
		14, 15, 16, 17,
		23, 24, 25, 26,
		32, 33, 34, 35
	];

	private array $colorSlots = [
		45, 46, 47, 48, 49, 50, 51, 52, 53,
	];

	/** @var DyeColor[] */
	private array $colors = [];

	private bool $isTicking = false;

	private bool $done = false;

	private ?DyeColor $player1Color = null;
	private ?DyeColor $player2Color = null;

	public function __construct(private Player $player1, private Player $player2){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Item Flip: " . $this->player1->getName() . " | " . $this->player2->getName());

		foreach($this->emptySlots as $slot){
			$menu->getInventory()->setItem($slot, $this->getEmptyItem());
		}

		$menu->getInventory()->setItem(0, $this->getAcceptItem());
		$menu->getInventory()->setItem(8, $this->getAcceptItem());
		$menu->getInventory()->setItem(4, $this->getInfoItem());

		$colors = array_values(DyeColor::getAll());
		shuffle($colors);

		/**
		 * @var int  $i
		* @var DyeColor  $color */
		foreach($colors as $i => $color){
			$slot = $this->colorSlots[$i] ?? null;

			if($slot === null) break;

			$this->colors[$slot] = $color;

			$menu->getInventory()->setItem($slot, $this->getColorItem($color));
		}

		return $menu;
	}

	public function onNormalTransaction(InvMenuTransaction $transaction) : InvMenuTransactionResult{
		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();
		$item = $transaction->getItemClicked();


		$acceptRejectSlot = 0;
		$editSlots = $this->player1Slots;
		$playerColor = &$this->player1Color;

		if($this->player2->getName() === $player->getName()) {
			$acceptRejectSlot = 8;
			$editSlots = $this->player2Slots;
			$playerColor = &$this->player2Color;
		}

		if($slot === $acceptRejectSlot){
			if(DyeColorIdMap::getInstance()->toId(DyeColor::RED()) === $item->getMeta()) {
				$this->getMenu()->getInventory()->setItem($slot, $this->getRejectItem());
			}

			if(DyeColorIdMap::getInstance()->toId(DyeColor::LIME()) === $item->getMeta()) {
				$this->getMenu()->getInventory()->setItem($slot, $this->getAcceptItem());
			}

			if(DyeColorIdMap::getInstance()->toId(DyeColor::LIME()) === $this->getMenu()->getInventory()->getItem(0) && DyeColorIdMap::getInstance()->toId(DyeColor::LIME()) === $this->getMenu()->getInventory()->getItem(8 )) {
				$this->startCountdown();
			}

			return $transaction->discard();
		}

		if(in_array($slot, $editSlots)){
			$this->cancel();
			return $transaction->continue();
		}

		if(in_array($slot, $this->colorSlots)){
			if($item->getId() === ItemIds::WOOL) {
				if($playerColor instanceof DyeColor){
					$s = array_search($playerColor, $this->colors);
					$this->getMenu()->getInventory()->setItem($s, $this->getColorItem($playerColor));
				}

				$playerColor = $this->colors[$slot];

				$this->getMenu()->getInventory()->setItem($slot, $this->getAcceptItem()->setCustomName("§e{$player->getName()}'s color: " . $this->colors[$slot]->getDisplayName()));
			}

			return $transaction->discard();
		}

		return $transaction->discard();
	}

	public function startCountdown(): void {
		Await::f2c(function(){
			while(true){
				yield $this->getStd()->sleep(20);

				$i1 = $this->getMenu()->getInventory()->getItem(0);
				$i2 = $this->getMenu()->getInventory()->getItem(8);
				if(DyeColorIdMap::getInstance()->toId(DyeColor::LIME()) === $this->getMenu()->getInventory()->getItem(0) && DyeColorIdMap::getInstance()->toId(DyeColor::LIME()) === $this->getMenu()->getInventory()->getItem(8 )) {

				}

				//TODO:
				//cancel
			}
		});
	}

	public function cancel(): void {

	}

	public function onClose(Player $player, Inventory $inventory) : void{
		if(!$this->done){
			foreach($inventory->getViewers() as $viewer){
				if($viewer->getName() === $player->getName()) continue;

				$player->removeCurrentWindow();
			}
		}
		parent::onClose($player, $inventory);
	}

	public function getColorItem(DyeColor $color): Item {
		return VanillaBlocks::WOOL()->setColor($color)->asItem()->setCustomName("§eSelect color: " . $color->getDisplayName());
	}

	public function getEmptyItem(): Item {
		return VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::BLACK())->asItem()->setCustomName(" ");
	}

	public function getAcceptItem(): Item {
		return VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::RED())->asItem()->setCustomName("§aClick to accept");
	}

	public function getRejectItem(): Item {
		return VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIME())->asItem()->setCustomName("§cClick me to cancel acceptation");
	}

	public function getInfoItem(): Item {
		return VanillaItems::NETHER_STAR()->setCustomName("§d§lInformation")->setLore([
			"§7Player 1: §c" . $this->player1->getName(),
			"§7Player 2: §c" . $this->player2->getName(),
		]);
	}
}