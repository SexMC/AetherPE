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
use skyblock\player\AetherPlayer;
use skyblock\utils\CustomEnchantUtils;

class CollectionTypeMenu extends AetherMenu {

	public function __construct(public array $collections, private AetherPlayer $player, private ?AetherMenu $aetherMenu = null){
		parent::__construct();
	}

	public function constructMenu() : InvMenu{
		$menu = $this->aetherMenu !== null ? $this->aetherMenu->getMenu() : InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->getInventory()->clearAll();
		$this->menu = $menu;

		$menu->setName("§r§d§lCollection");
		$array = array_merge(range(0, 8), range(0, 45, 9), range(8, 53, 9), range(45, 53));

		foreach($array as $v){
			$menu->getInventory()->setItem($v, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
		}

		$session = $this->player->getCurrentProfile()->getProfileSession();

		/**
		 * @var  $k
		* @var Collection  $v */
		foreach($this->collections as $k => $v){
			$lvl = $session->getCollectionLevel($v->getName());
			$count = $session->getCollectionCount($v->getName());

			if($count === 0 && $lvl === 0){
				$menu->getInventory()->addItem($this->getNotUnlockedItem($v));
				continue;
			}

			$item = clone $v->getItem();
			$item->getNamedTag()->setByte("collection_key", $k);
			$item->setCustomName("§r§e" . $item->getName());
			if($lvl === $v->getMaxLevel()){
				$item->setLore([
					"§r§7View all your §e{$item->getName()}§7 collection",
					"§r§7progress and rewards!",
				]);
			} else {
				$next = CustomEnchantUtils::roman($lvl + 1);
				$lore = [
					"§r§7View all your §e{$item->getName()}§7 collection",
					"§r§7progress and rewards!",
					"§r",
					"§r§7Progress to {$v->getName()} $next: §e" . number_format(($count/$v::getNeededForLevel($lvl + 1)*100), 2) . "%",
					"§r§7",
					"§r§7Rewards:",
				];

				foreach($v::getRewardsAsString($v->getUnlockRecipes()[$lvl + 1]) as $string){
					$lore[] = "§r§g- $string";
				}

				$item->setLore($lore);


			}


			$menu->getInventory()->addItem($item);
		}


		$menu->getInventory()->setItem(49, VanillaBlocks::BARRIER()->asItem()->setCustomName("§r§cClose"));
		$menu->getInventory()->setItem(48, VanillaItems::ARROW()->setCustomName("§r§aGo Back")->setLore(["§r§7To collection menu"]));


		return $menu;
	}

	public function getNotUnlockedItem(Collection $collection): Item {
		$item = VanillaItems::GRAY_DYE();
		$item->setCustomName("§r§c{$collection->getName()}");
		$item->setLore([
			"§r§7Find this item to add it to your",
			"§r§7collection and unlock collection",
			"§r§7rewards!",
		]);

		return $item;
	}

	public function onReadonlyTransaction(InvMenuTransaction $transaction) : void{
		$player = $transaction->getPlayer();
		$clicked = $transaction->getItemClicked();
		$slot = $transaction->getAction()->getSlot();

		$key = $clicked->getNamedTag()->getByte("collection_key", -1);

		if($key !== -1){
			if(isset($this->collections[$key])){
				(new CollectionViewMenu($player, $this->collections[$key], $this))->send($player);
			}
		}

		if($slot === 49){
			$player->removeCurrentWindow();
		}

		if($slot === 48){
			(new CollectionMenu($player, $this))->send($player);
		}
	}
}