<?php

declare(strict_types=1);

namespace skyblock\menus\recipe;


use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\menus\common\ViewPagedItemsMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\player\AetherPlayer;

class RecipeByClassMenu extends AetherMenu {

	private array $ids = [
		"farming" => ItemIds::GOLDEN_HOE,
		"mining" => ItemIds::STONE_PICKAXE,
		"combat" => ItemIds::STONE_SWORD,
		"fishing" => ItemIds::FISHING_ROD,
		"foraging" => ItemIds::SAPLING,
		"alchemy" => ItemIds::BREWING_STAND,
		"enchanting" => ItemIds::ENCHANTING_TABLE,
		"unclassified" => ItemIds::ELEMENT_0,
	];

	private array $slots = [
		"farming" => 20,
		"mining" => 21,
		"combat" => 22,
		"fishing" => 23,
		"foraging" => 24,
		"alchemy" => 30,
		"enchanting" => 32,
		"unclassified" => 31,
	];


	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;


		$menu->setName("Recipes");



		foreach($this->ids as $key => $id){
			$item = ItemFactory::getInstance()->get($id);
			$item->getNamedTag()->setString("class", $key);
			$item->setCustomName("§r§a§l» " . ucwords($key) . " Recipes «");
			$item->setLore([
				"§r§7View all of the $key recipes",
				"§r§7you have unlocked!",
				"§r",
				"§r§eClick to view!"
			]);
			
			$menu->getInventory()->setItem($this->slots[$key], $item);
		}


		foreach($menu->getInventory()->getContents(true) as $i => $v){
			if($v->isNull()){
				$menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
			}
		}

		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));
		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§7Go back")->setLore(["§r§7To SkyBlock Menu"]));



		return $menu;
	}



	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$item = $transaction->getOut();

		$class = $item->getNamedTag()->getString("class", "");


		$slot = $transaction->getAction()->getSlot();


		if($slot === 49){
			$player->removeCurrentWindow();
		}

		if($slot === 48){
			(new SkyblockMenu($player, $this))->send($player);
		}

		if($class !== ""){
			(new RecipesViewMenu($player, $this, -1, RecipesHandler::getInstance()->getClassified()[$class] ?? []))->send($player);
		}
	}
}