<?php

declare(strict_types=1);

namespace skyblock\menus\recipe;


use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use skyblock\misc\recipes\RecipeCraftingInstance;
use skyblock\Main;
use skyblock\menus\AetherMenu;
use skyblock\misc\recipes\Recipe;
use skyblock\sessions\Session;
use skyblock\utils\TimeUtils;

class RecipesConfirmMenu extends AetherMenu {

	private array $slots = [
		12, 13, 14,
		21, 22, 23,
		30, 31, 32
	];

	private int $outputSlot = 25;
	private int $confirmSlot = 49;

	public function __construct(private Recipe $recipe, private int $slot, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();

		$menu->setName("Recipes");

		$v = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");
		for($i = 0; $i <= 53; $i++){
			if(in_array($i, $this->slots)) continue;
			$menu->getInventory()->setItem($i, $v);
		}

		foreach($this->recipe->getInputWithPositions() as $k => $v){
			$menu->getInventory()->setItem($this->slots[$k], $v);
		}

		$menu->getInventory()->setItem(0, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§7Go back"));


		$menu->getInventory()->setItem($this->outputSlot, $this->recipe->getViewItem());
		if($this->slot !== -1){
			$menu->getInventory()->setItem($this->confirmSlot, $this->getConfirmItem());
		} else $menu->getInventory()->setItem($this->confirmSlot, $this->getBackItem());

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();

		if($slot === 0){
			if($this->slot === -1){
				(new RecipeByClassMenu($player, $this))->send($player);
				return;
			}

			(new RecipesMenu($player, $this));
			return;
		}

		if($slot !== $this->confirmSlot){
			return;
		}

		if($this->slot === -1){
			(new RecipeByClassMenu($player, $this))->send($player);
			return;
		}

		if(!$this->recipe->meetsRequirements($player)){
			$player->sendMessage(Main::PREFIX . "You don't have the required items in your inventory");
			return;
		}

		foreach($this->recipe->getInput() as $input){
			$player->getInventory()->removeItem($input);
		}

		$session = new Session($player);
		$recipes = $session->getActiveRecipes();
		$recipes[$this->slot] = new RecipeCraftingInstance($this->recipe->getName(), time(), $this->slot);
		$session->setRecipes($recipes);

		(new RecipesMenu($player, $this));
	}

	public function getBackItem(): Item {
		return VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cGo back");
	}

	public function getConfirmItem(): Item {
		$item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIME())->asItem();
		$item->setCustomName("§r§l§aClick to confirm crafting");
		$item->setLore([
			"§r§7Click to start crafting this recipe",
			"§r",
			"§r§7Recipe: §c" . $this->recipe->getName(),
		]);

		return $item;
	}

}