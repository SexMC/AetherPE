<?php

declare(strict_types=1);

namespace skyblock\menus\recipe;


use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\VanillaItems;
use skyblock\items\special\SpecialItem;
use skyblock\items\special\types\minion\MinionEgg;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\menus\common\ViewPagedItemsMenu;
use skyblock\misc\recipes\Recipe;
use skyblock\misc\recipes\RecipesHandler;
use skyblock\player\AetherPlayer;

class RecipesViewMenu extends AetherMenu {

	private array $contents = [];

	private int $currentPage = 0;

	public function __construct(private AetherPlayer $player, private ?AetherMenu $aetherMenu = null, private int $slot = -1, private ?array $all = null){
		$list = $this->player->getCurrentProfile()->getProfileSession()->getAllUnlockedRecipesIdentifiers();


		foreach($all ?? $this->player->getCurrentProfile()->getProfileSession()->getAllUnlockedRecipesIdentifiers() as $recipeId){
			$recipe = $recipeId instanceof Recipe ? $recipeId : RecipesHandler::getInstance()->getRecipe($recipeId);

			if($recipe === null){
				Main::debug("Got unexesting recipe $recipeId");
				continue;
			}

			if(!$recipe->isAutoUnlocked()){
				if($this->slot !== -1){
					if(!in_array($recipe->getName(), $list)) continue;
					if(!$recipe->meetsRequirements($this->player)) continue;
				}
			}


			$i = $recipe->getViewItem();

			$lore = $i->getLore();
			$lore[] = "§r";
			$lore[] = "§r§7Unlocked: §l" . (in_array($recipe->getName(), $list) ? "§aYes" : "§cNO");
			$lore[] = "§r§7Source: §l§e" . (RecipesHandler::getInstance()->getUnlockingByRecipe($recipe));
			$i->setLore($lore);



			$this->contents[] = $i;
		}

		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$this->contents = array_chunk($this->contents, 43);

		$menu->setName("Recipes");

		for($i = 0; $i <= 8; $i++){
			$menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
		}

		$menu->getInventory()->setItem(0, VanillaItems::ARROW()->setCustomName("§r§7Go back"));


		if($this->slot !== -1){
			$menu->getInventory()->setItem(4, VanillaItems::BOOK()->setCustomName("§r§l§aSelect a recipe")->setLore([
				"§r§7All your available recipes are displayed below",
				"§r§7Right-click one to continue crafting",
			]));
		}

		$this->setCurrentPageItems();

		return $menu;
	}

	public function setCurrentPageItems(): void {
		if(!isset($this->contents[$this->currentPage])) return;
		for($i = 9; $i <= 53; $i++){
			$this->getMenu()->getInventory()->clear($i);
		}

		$current = "§c" . ($this->currentPage + 1) . "§7/§c" . sizeof($this->contents);

		$this->getMenu()->getInventory()->setItem(52, VanillaItems::ARROW()->setCustomName("§7<- Back ($current)"));
		$this->getMenu()->getInventory()->setItem(53, VanillaItems::ARROW()->setCustomName("§7Next Page ($current) ->"));

		$entries = $this->contents[$this->currentPage];

		foreach($entries as $item){
			$this->getMenu()->getInventory()->addItem($item);
		}
	}


	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$item = $transaction->getOut();
		$slot = $transaction->getAction()->getSlot();


		if(SpecialItem::getSpecialItem($item) instanceof MinionEgg && $this->slot === -1){
			$all = [];

			for($i = 9; $i <= 18; $i++){
				if(SpecialItem::getSpecialItem($this->getMenu()->getInventory()->getItem($i)) instanceof MinionEgg){
					$all[] = true;
				}
			}

			if(sizeof($all) < 10){
				(new RecipesViewMenu($player, $this, -1, MinionEgg::getAllLevelItemsByEggItem($item)))->send($player);

				return;
			}
		}

		if(($v =$item->getNamedTag()->getString("recipe_name", "")) !== ""){
			$recipe = RecipesHandler::getInstance()->getRecipe($v);
			if($recipe === null) return;


			(new RecipesConfirmMenu($recipe, $this->slot, $this));
		}

		if($slot === 0){
			if($this->slot === -1){
				(new RecipeByClassMenu($player, $this))->send($player);

				return;
			}

			(new RecipesMenu($player, $this));
			return;
		}

		if($transaction->getAction()->getSlot() === 52){
			if($this->currentPage === 0){
				$this->currentPage = sizeof($this->contents) - 1;
				$this->setCurrentPageItems();
				return;
			}

			if(isset($this->contents[$this->currentPage - 1])){
				$this->currentPage--;
				$this->setCurrentPageItems();
			}
		} elseif($transaction->getAction()->getSlot() === 53){
			if($this->currentPage === sizeof($this->contents) - 1){
				$this->currentPage = 0;
				$this->setCurrentPageItems();
				return;
			}

			if(isset($this->contents[$this->currentPage + 1])){
				$this->currentPage++;
				$this->setCurrentPageItems();
			}
		}
	}
}