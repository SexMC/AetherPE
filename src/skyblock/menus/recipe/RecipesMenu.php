<?php

declare(strict_types=1);

namespace skyblock\menus\recipe;


use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use skyblock\menus\AetherMenu;
use skyblock\menus\items\SkyblockMenu;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class RecipesMenu extends AetherMenu {
	use AwaitStdTrait;

	private bool $active = false;

	private array $recipeSlots = [
		28, 29, 30, 31, 32, 33, 34,
		37, 38, 39, 40, 41, 42, 43
	];

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function onClose(Player $player, Inventory $inventory) : void{
		parent::onClose($player, $inventory);
		$this->active = false;
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$menu->setName("Recipes");
		$this->active = true;
		
		$menu->getInventory()->setItem(0, $this->getInfoItem());
		$menu->getInventory()->setItem(8, $this->getViewRecipe());


		$session = new Session($this->player);
		$recipes = $session->getActiveRecipes();
		foreach($this->recipeSlots as $k => $slot){
			if($k >= 3){
				$menu->getInventory()->setItem($slot, $this->getNotUnlocked());
				continue;
			}

			if(isset($recipes[$slot])){
				$activeRecipe = $recipes[$slot];

				if($v = $activeRecipe->getViewItem()){
					$menu->getInventory()->setItem($slot, $v);
					continue;
				}
			}

			$menu->getInventory()->setItem($slot, $this->getCraftItem());
		}

		Await::f2c(function() use($menu) {
			$session = new Session($this->player);

			while($this->active){
				$recipes = $session->getActiveRecipes();
				foreach($this->recipeSlots as $k => $slot){
					if($k >= 3){
						$menu->getInventory()->setItem($slot, $this->getNotUnlocked());
						continue;
					}

					if(isset($recipes[$slot])){
						$activeRecipe = $recipes[$slot];

						if($v = $activeRecipe->getViewItem()){
							$menu->getInventory()->setItem($slot, $v);
							continue;
						}
					}

					$menu->getInventory()->setItem($slot, $this->getCraftItem());
				}

				yield $this->getStd()->sleep(20);
			}
		});

		$i = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem();
		foreach($menu->getInventory()->getContents(true) as $slot => $content){
			if($content->isNull()){
				$menu->getInventory()->setItem($slot, $i);
			}
		}

		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To SkyBlock menu"]));


		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$slot = $transaction->getAction()->getSlot();
		$player = $transaction->getPlayer();
		$item = $transaction->getOut();
		assert($player instanceof AetherPlayer);

		if($slot === 8){
			(new RecipeByClassMenu($player, $this))->send($player);

			$this->active = false;
		}

		if($slot === 48){
			(new SkyblockMenu($player, $this))->send($this->player);
			$this->active = false;
			return;
		}

		if($item->getNamedTag()->getByte("recipe_finished_claimable", 0) === 1){
			$s = new Session($player);
			$recipes = $s->getActiveRecipes();
			if(!isset($recipes[$slot])) return;
			$recipe = RecipesHandler::getInstance()->getRecipe($recipes[$slot]->recipeName);

			unset($recipes[$slot]);
			$s->setRecipes($recipes);

			$item->getNamedTag()->removeTag("recipe_finished_claimable");
			Utils::addItem($player, $recipe->getOutput());
			$this->menu->getInventory()->setItem($slot, $this->getCraftItem());
		}

		if($item->getId() === ItemIds::CRAFTING_TABLE){
			(new RecipesViewMenu($player, $this, $slot));
			$this->active = false;
		}
	}

	public function getCraftItem(): Item {
		$item = VanillaBlocks::CRAFTING_TABLE()->asItem();
		$item->setCustomName("§r§l§dAvailable crafting slot");
		$item->setLore([
			"§r§7Right click to start crafting"
		]);

		return $item;
	}

	public function getInfoItem(): Item {
		$item = VanillaItems::BOOK();
		$item->setCustomName("§r§b§lWhat is this?");
		$item->setLore([
			"§r§7Using this menu, you can craft items that",
			"§r§7are not found in the galaxy.",
			"§r§7",
			"§r§7You can find recipe unlock papers",
			"§r§7in the grinding world and in lootboxes.",
		]);

		return $item;
	}

	public function getNotUnlocked(): Item {
		$item = VanillaBlocks::BARRIER()->asItem();
		$item->setCustomName("§r§c§lLocked Recipe Crafting Slot");
		$item->setLore([
			"§r§7You can unlock this slot by having",
			"§r§7higher rank (store.aetherpe.net) or by",
			"§r§7using recipe crafting slot expanders",
		]);

		return $item;
	}

	public function getViewRecipe(): Item {
		$item = VanillaBlocks::ELEMENT_ZERO()->asItem();
		$item->setCustomName("§r§g§lView recipes.");
		$item->setLore([
			"§r§7Right-click to view all recipes",
		]);

		return $item;
	}
}