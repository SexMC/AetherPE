<?php

declare(strict_types=1);

namespace skyblock\menus\collection;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\misc\collection\CollectionHandler;
use skyblock\player\AetherPlayer;

class CollectionMenu extends AetherMenu {

	private array $slots = [20, 21, 22, 23, 24];

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("§r§d§lCollection");



		$menu->getInventory()->setItem(21, VanillaItems::STONE_PICKAXE()->setCustomName("§r§gMining Collection"));
		$menu->getInventory()->setItem(20, VanillaItems::GOLDEN_HOE()->setCustomName("§r§aFarming Collection"));
		$menu->getInventory()->setItem(22, VanillaItems::STONE_SWORD()->setCustomName("§r§cCombat Collection"));
		$menu->getInventory()->setItem(23, VanillaBlocks::JUNGLE_SAPLING()->asItem()->setCustomName("§r§dForaging Collection"));
		$menu->getInventory()->setItem(24, VanillaItems::FISHING_ROD()->setCustomName("§r§bFishing Collection"));
		$menu->getInventory()->setItem(31, VanillaItems::WITHER_SKELETON_SKULL()->setCustomName("§r§4Boss Collection §o§7(§bComing Soon§7)"));

		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));
		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock menu"]));
		//TODO: implemented going back to sb menu ^
		
		
		foreach($menu->getInventory()->getContents(true) as $k => $c){
			if($c->isNull()){
				$menu->getInventory()->setItem($k, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
			}
		}

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$player = $transaction->getPlayer();

		$arr = null;
		if($slot === 20) {
			$arr = CollectionHandler::getInstance()->getCollectionType("farming");
		}

		if($slot === 21) {
			$arr = CollectionHandler::getInstance()->getCollectionType("mining");
		}

		if($slot === 22) {
			$arr = CollectionHandler::getInstance()->getCollectionType("combat");
		}

		if($slot === 23) {
			$arr = CollectionHandler::getInstance()->getCollectionType("foraging");
		}

		if($slot === 24) {
			$arr = CollectionHandler::getInstance()->getCollectionType("fishing");
		}

		if($arr !== null){
			(new CollectionTypeMenu($arr, $player, $this))->send($player);
		}

		if($slot === 49){
			$player->removeCurrentWindow();
		}

		if($slot === 48){
			(new SkyblockMenu($this->player, $this))->send($this->player);
		}
	}
}