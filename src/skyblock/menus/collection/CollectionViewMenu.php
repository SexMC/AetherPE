<?php

declare(strict_types=1);

namespace skyblock\menus\collection;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;
use skyblock\misc\collection\Collection;
use skyblock\misc\collection\CollectionHandler;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\CustomEnchantUtils;

class CollectionViewMenu extends AetherMenu {

	public function __construct(private AetherPlayer $player, private Collection $collection, private AetherMenu $aetherMenu){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$fillSlots = array_merge(range(17, 26), range(27, 29));
		$session = $this->player->getCurrentProfile()->getProfileSession();
		$lvl = $session->getCollectionLevel($this->collection->getName());
		$count = $session->getCollectionCount($this->collection->getName());

		foreach($this->collection->getUnlockRecipes() as $k => $v){
			$current = $k;
			$item = VanillaBlocks::STAINED_GLASS_PANE()->setColor(($lvl >= $current ? DyeColor::GREEN() : ($current === $lvl + 1 ? DyeColor::YELLOW() : DyeColor::RED())))->asItem();
			$color = ($lvl > $current ? "§a" : ($lvl < $current ? "§c" : "§e"));
			$colorLore = $lvl > $current ? "§a" : "§e";
			$item->getNamedTag()->setByte("collection_index", $k);
			$item->setCustomName("§r$color" . $this->collection->getName() . " " . CustomEnchantUtils::roman($current));
			$lore = [
				"§r",
				"§r§7Progress: " . $colorLore . number_format($count/$this->collection::getNeededForLevel($current)*100, 2) . "%",
				"§r§7(§e" . number_format($count) . "§6/§e" . number_format($this->collection::getNeededForLevel($current)) . "§7)",
				"§r§7",
				"§r§7Rewards:",
			];

			foreach($this->collection::getRewardsAsString($v) as $string){
				$lore[] = "§r§g- $string";
			}

			$item->setLore($lore);
			$item->setCount($current);


			$menu->getInventory()->setItem($fillSlots[$k], $item);
		}

		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));
		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To collection type"]));


		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$clicked = $transaction->getItemClicked();
		$slot = $transaction->getAction()->getSlot();

		$key = $clicked->getNamedTag()->getByte("collection_index", -1);

		if($key !== -1){
			$r = $this->collection->getUnlockRecipes()[$clicked->getCount()];

			if(!is_array($r)){
				$r = [$r];
			}

			(new CollectionViewRewardsMenu($player, $this->collection, $r, $this))->send($player);
		}

		if($slot === 49){
			$player->removeCurrentWindow();
		}

		if($slot === 48){
			(new CollectionTypeMenu(CollectionHandler::getInstance()->getCollectionType(CollectionHandler::getInstance()->getCategoryByCollection($this->collection)), $player, $this))->send($player);
		}

	}
}