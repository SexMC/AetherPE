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
use skyblock\misc\trades\Trade;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use skyblock\utils\CustomEnchantUtils;

class CollectionViewRewardsMenu extends AetherMenu {

	private array $freeSlots = [22, 23, 21, 24, 20, 25, 19, 26, 18];

	public function __construct(private AetherPlayer $player, private Collection $collection, private array $rewards, private AetherMenu $aetherMenu){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		foreach($this->rewards as $k => $reward){
			if($reward instanceof Item){
				$menu->getInventory()->setItem($this->freeSlots[$k], $reward);
			} elseif($reward instanceof Trade){
				$menu->getInventory()->setItem($this->freeSlots[$k], $reward->getViewItem());
			} else {
				$item = VanillaItems::WRITTEN_BOOK();
				$item->setCustomName("§r§a" . $reward);
				$menu->getInventory()->setItem($this->freeSlots[$k], $item);
			}
		}

		$session = $this->player->getCurrentProfile()->getProfileSession();

		$v = $this->collection;
		$lvl = $session->getCollectionLevel($v->getName());
		$count = $session->getCollectionCount($v->getName());

		$item = clone $v->getItem();
		$item->setCustomName("§r§e" . $item->getName());
		if($lvl === $v->getMaxLevel()){
			$item->setLore([
				"§r§7View all your §e{$item->getName()}§7 collection",
				"§r§7progress and rewards!",
			]);
		} else{
			$next = CustomEnchantUtils::roman($lvl + 1);
			$lore = [
				"§r§7View all your §e{$item->getName()}§7 collection",
				"§r§7progress and rewards!",
				"§r",
				"§r§7Progress to {$v->getName()} $next: §e" . number_format(($count / $v::getNeededForLevel($lvl + 1) * 100), 2) . "%",
				"§r§7",
				"§r§7Rewards:",
			];

			foreach($v::getRewardsAsString($v->getUnlockRecipes()[$lvl + 1]) as $string){
				$lore[] = "§r§g- $string";
			}

			$item->setLore($lore);
		}
		$menu->getInventory()->setItem(4, $item);
			
		

		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));
		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To {$this->collection->getName()} collection"]));


		foreach($menu->getInventory()->getContents(true) as $k => $c){
			if($c->isNull()){
				$menu->getInventory()->setItem($k, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
			}
		}

		return $menu;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$slot = $transaction->getAction()->getSlot();

		if($slot === 49){
			$player->removeCurrentWindow();
		}

		if($slot === 48){
			(new CollectionViewMenu($player, $this->collection, $this))->send($player);
		}
	}
}