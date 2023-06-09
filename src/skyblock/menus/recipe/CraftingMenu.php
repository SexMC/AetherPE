<?php

declare(strict_types=1);

namespace skyblock\menus\recipe;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\inventory\CraftingTableInventory;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\crafting\CraftingGrid;
use pocketmine\crafting\CraftingRecipe;
use pocketmine\crafting\ShapedRecipe;
use pocketmine\crafting\ShapelessRecipe;
use pocketmine\crafting\ShapelessRecipeType;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\menus\AetherMenu;
use skyblock\player\AetherPlayer;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;


//TODO: optimize and clean up code
class CraftingMenu extends AetherMenu {
	use AwaitStdTrait;

	private array $allRecipes = [];

	private int $page = 1;

	private bool $active = true;

	public function onClose(Player $player, Inventory $inventory) : void{
		parent::onClose($player, $inventory);

		$this->active = false;
	}

	public function __construct(AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();

		$crafting = Server::getInstance()->getCraftingManager();

		foreach($crafting->getShapelessRecipes() as $l){
			foreach($l as $rec){
				if($rec->getType()->id() == ShapelessRecipeType::CRAFTING()->id()){
					$this->allRecipes[] = $rec;
				}
			}
		}

		foreach($crafting->getShapedRecipes() as $l){
			foreach($l as $rec){
				array_unshift($this->allRecipes, $rec);
			}
		}

		$this->availabeCraftsList = $this->getAllPossibleCrafts($player->getInventory());
		$this->updatePossibleCrafts();

		$this->setOutput(VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cNo output"));

		Await::f2c(function() use($player) {
			while($this->active && $player->isOnline()){
				$data = yield Await::race([
					$this->getStd()->awaitEvent(PlayerDropItemEvent::class, fn(PlayerDropItemEvent $event) => $event->getPlayer()->getId() === $player->getId(), true, EventPriority::LOWEST, true),
					$this->getStd()->awaitEvent(EntityItemPickupEvent::class, fn(EntityItemPickupEvent $event) => $event->getEntity()->getId() === $player->getId(), true, EventPriority::LOWEST, true),
				]);


				if($this->active && $player->isOnline()){
					$data[1]->cancel();
				}
			}
		});

		$this->scheduleUpdate(false, true);
	}

	protected $type = self::NORMAL;

	private array $slots = [
		10, 11, 12,
		19, 20, 21,
		28, 29, 30,
	];

	/** @var ShapedRecipe[]|ShapelessRecipe[]  */
	private array $availabeCraftsList = [];
	/** @var int[] */
	private array $availableCrafts = [46, 47, 48, 49, 50, 51, 52];

	private int $outputSlot = 25;

	private bool $scheduled = false;

	private int $previousPage = 45;
	private int $nextPage = 53;

	public function scheduleUpdate(bool $force = false, bool $possibleCrafts = false): void {
		if($force){
			$this->updateMenu();
			return;
		}

		if(!$this->scheduled){
			Await::f2c(function() use($possibleCrafts) {
				$this->scheduled = true;
				yield $this->getStd()->sleep(2);
				$this->updateMenu();
				if($possibleCrafts){
					$this->updatePossibleCrafts();
				}

				$this->scheduled = false;
			});
		}


	}

	public function getNextPageItem(int $page, int $totalPages): Item {
		$item = VanillaBlocks::REDSTONE_TORCH()->asItem();

		$item->setCustomName("§r§aNext Page §7(§e{$page}§6/§e{$totalPages}§7)");
		$item->setLore([
			"§r§7Go to the next page of the possible",
			"§r§7items you can craft with the",
			"§r§7ingredients in your inventory.",
			"",
			"§r§7Current Page: §e" . $page,
			"§r§7Total Pages: §e" . $totalPages,
		]);

		return $item;
	}

	public function getPreviousPageItem(int $page, int $totalPages): Item {
		$item = VanillaBlocks::REDSTONE_TORCH()->asItem();

		$item->setCustomName("§r§aPrevious Page §7(§e{$page}§6/§e{$totalPages}§7)");
		$item->setLore([
			"§r§7Go to the previous page of the possible",
			"§r§7items you can craft with the",
			"§r§7ingredients in your inventory.",
			"",
			"§r§7Current Page: §e" . $page,
			"§r§7Total Pages: §c" . $totalPages,
		]);

		return $item;
	}

	public function removeItems(CraftingRecipe $recipe): void {
		foreach($recipe->getIngredientList() as $k => $v){

			foreach($this->slots as $slot){
				$item = $this->getMenu()->getInventory()->getItem($slot);

				if($item->equals($v, !$v->hasAnyDamageValue(), $v->hasNamedTag())){
					$item->setCount($item->getCount() - $v->getCount());
					$this->getMenu()->getInventory()->setItem($slot, $item);
					break;
				}
			}
		}
	}

	public function updatePossibleCrafts(): void {
		$crafts = $this->availabeCraftsList;

		$chunked = array_chunk($crafts, 7);
		$current = $this->page;
		$max = count($chunked);

		if($current > $chunked){
			$current = 1;
		}

		$inventory = $this->getMenu()->getInventory();

		$inventory->setItem($this->nextPage, $this->getNextPageItem($current, $max));
		$inventory->setItem($this->previousPage, $this->getPreviousPageItem($current, $max));

		if(!isset($chunked[$current-1])){
			return;
		}


		foreach($this->availableCrafts as $key => $slot){
			$r = $chunked[$current-1][$key] ?? null;
			if($r === null){
				$r = VanillaItems::AIR();
			} else $r = $r->getResults()[0];

			$inventory->setItem($slot, $r);
		}
	}

	public function containsItem(Inventory $inventory, Item $item) : bool{
		$count = max(1, $item->getCount());
		foreach($inventory->getContents() as $i){
			if($item->equals($i,!$item->hasAnyDamageValue(), true)){
				$count -= $i->getCount();
				if($count <= 0){
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @return CraftingRecipe[]
	 */
	public function getAllPossibleCrafts(PlayerInventory $inventory): array {
		$recipes = [];

		$grid = new CraftingTableInventory(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());

		foreach($this->slots as $k => $v){
			$grid->setItem($k, $this->getMenu()->getInventory()->getItem($v));
		}

		foreach($this->allRecipes as $recipe){

				$totalIngredients = [];

				/** @var Item $ingredient */
				foreach($recipe->getIngredientList() as $ingredient){
					$id = $ingredient->getId() . $ingredient->getMeta();
					if(isset($totalIngredients[$id])){
						$totalIngredients[$id]->setCount($totalIngredients[$id]->getCount() + $ingredient->getCount());
					} else $totalIngredients[$id] = $ingredient;
				}

				$containsAll = true;
				foreach($totalIngredients as $ingredient){
					//if($ingredient->getName() === "Unknown") continue 2;
					if(!$this->containsItem($inventory, $ingredient)){
						$containsAll = false;
						break;
					}
				}

				if($containsAll){
					$recipes[] = $recipe;
				}
		}

		return $recipes;
	}

	public function setOutput(Item $item ): void {
		$this->getMenu()->getInventory()->setItem($this->outputSlot, $item);
	}

	public function updateMenu(bool $takeFoundRecipeItems = false): ?CraftingRecipe {
		$grid = new CraftingTableInventory(Server::getInstance()->getWorldManager()->getDefaultWorld()->getSpawnLocation());

		foreach($this->slots as $k => $v){
			$grid->setItem($k, $this->getMenu()->getInventory()->getItem($v));
		}

		$list = $this->allRecipes;

		$found = null;
		foreach($list as $k => $recipe){
				if($recipe instanceof ShapedRecipe){
					if($this->matchesBoth($recipe, $grid)){
						$found = $recipe;
						break;
					}
				}

				if($recipe instanceof ShapelessRecipe){
					if($recipe->matchesCraftingGrid($grid)){
						$found = $recipe;
						break;
					}
				}

		}

		if($found === null){
			$this->setOutput(VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cNo output"));
			return null;
		}

		$this->setOutput($found->getResults()[0]);

		if($takeFoundRecipeItems){
			$this->removeItems($found);
		}

		return $found;
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;
		$menu->setName("crafting");

		$v = VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" ");
		for($i = 0; $i <= 53; $i++){
			if(in_array($i, $this->slots)) continue;
			$menu->getInventory()->setItem($i, $v);
		}

		foreach($this->slots as $slot){
			$menu->getInventory()->setItem($slot, VanillaItems::AIR());
		}


		return $menu;
	}

	public function onCraft(AetherPlayer $player, CraftingRecipe $recipe): void {
		$this->scheduleUpdate(false, true);

		$this->availabeCraftsList = $this->getAllPossibleCrafts($player->getInventory());
	}

	public function onNormalTransaction(InvMenuTransaction $transaction) : InvMenuTransactionResult{
		$slot = $transaction->getAction()->getSlot();
		$out = $transaction->getOut();
		$in = $transaction->getIn();
		$player = $transaction->getPlayer();

		if($slot === $this->outputSlot){
			if($transaction->getIn()->isNull()){
				$this->updateMenu();

				if($this->getMenu()->getInventory()->getItem($this->outputSlot)->equals($out)){
					$recipe = $this->updateMenu(true);

					if($recipe === null){
						return $transaction->discard();
					}


					$this->onCraft($player, $recipe);

					return $transaction->continue();
				}

			}

			return $transaction->discard();
		}

		if($slot === $this->nextPage){
			$this->page = min(count(array_chunk($this->availabeCraftsList, 7)), $this->page + 1);
			$this->updatePossibleCrafts();

			return $transaction->discard();
		}

		if($slot === $this->previousPage){
			$this->page = max(1, $this->page - 1);
			$this->updatePossibleCrafts();


			return $transaction->discard();
		}

		if(in_array($slot, $this->availableCrafts)){
			if(!$in->isNull()){
				return $transaction->discard();
			}

			if($out->isNull()){
				return $transaction->discard();
			}

			$this->availabeCraftsList = $this->getAllPossibleCrafts($player->getInventory());

			$found = null;
			foreach($this->availabeCraftsList as $recipe){
				if($recipe->getResults()[0]->equals($out, !$out->hasAnyDamageValue(), $out->hasNamedTag())){
					$found = $recipe;
					break;
				}
			}

			if($found === null){
				return $transaction->discard();
			}
			
			foreach($found->getIngredientList() as $list){
				if(!$player->getInventory()->contains($list)) return $transaction->discard();
			}

			foreach($found->getIngredientList() as $list){
				$player->getInventory()->removeItem($list);
			}

			$this->availabeCraftsList = $this->getAllPossibleCrafts($player->getInventory());
			$this->scheduleUpdate(false, true);
			return $transaction->continue();
		}


		$this->scheduleUpdate();


		if(in_array($slot, $this->slots)){
			return $transaction->continue();
		}

		return $transaction->discard();
	}



	//copied from PM recipe class itself

	public function matchesBoth(ShapedRecipe $recipe, CraftingGrid $grid): bool {
		if($recipe->getWidth() !== $grid->getRecipeWidth() || $recipe->getHeight() !== $grid->getRecipeHeight()){
			return false;
		}

		return $this->matches($recipe, $grid, true) && $this->matches($recipe, $grid, false);
	}

	public function matches(ShapedRecipe $recipe, CraftingGrid $grid, bool $reverse = false): bool {
		for($y = 0; $y < $recipe->getHeight(); ++$y){
			for($x = 0; $x < $recipe->getWidth(); ++$x){

				$given = $grid->getIngredient($reverse ? $recipe->getWidth() - $x - 1 : $x, $y);
				$required = $recipe->getIngredient($x, $y);


				if(!$required->equals($given, !$required->hasAnyDamageValue(), $required->hasNamedTag()) || $required->getCount() > $given->getCount()){
					return false;
				}
			}
		}

		return true;
	}
}