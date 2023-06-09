<?php

declare(strict_types=1);

namespace skyblock\menus\recipe;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\inventory\Inventory;
use pocketmine\item\ItemIds;
use pocketmine\item\VanillaItems;
use skyblock\menus\AetherMenu;
use skyblock\tiles\BrewingStandTile;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class BrewingMenu extends AetherMenu {
	use AwaitStdTrait;

	protected $type = self::NORMAL;

	private int $ingredientSlot = 13;

	private array $slots = [38, 40, 42];

	public function __construct(private BrewingStandTile $tile){
		parent::__construct();


		Await::f2c(function() {
			while(!$this->closed){
				$bool = $this->tile->update();
				$this->update($this->getMenu()->getInventory());

				if($bool) {
					$meta = $this->getMenu()->getInventory()->getItem(20)->getMeta();
					foreach(array_merge(range(20, 24), [29, 31, 33]) as $i){
						$this->getMenu()->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor($meta === 3 ? DyeColor::ORANGE() : DyeColor::LIGHT_BLUE())->asItem()->setCustomName("§r§eBrewing..."));
					}
				} else {
					foreach(array_merge(range(20, 24), [29, 31, 33]) as $i){
						$this->getMenu()->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIGHT_BLUE())->asItem()->setCustomName(" "));
					}
				}

				yield $this->getStd()->sleep(5);
			}
		});
	}

	public function constructMenu() : InvMenu{
		$menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
		$menu->setName("Brewing Stand");

		for($i = 0; $i <= 53; $i++){
			$menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::GRAY())->asItem()->setCustomName(" "));
		}

		foreach(array_merge(range(20, 24), [29, 31, 33]) as $i){
			$menu->getInventory()->setItem($i, VanillaBlocks::STAINED_GLASS_PANE()->setColor(DyeColor::LIGHT_BLUE())->asItem()->setCustomName(" "));
		}

		$this->update($menu->getInventory());

		return $menu;
	}

	public function getCoresspondingSlot(int $slot): int {
		return match($slot) {
			13 => 0,
			38 => 1,
			40 => 2,
			42 => 3,
		};
	}

	public function onNormalTransaction(InvMenuTransaction $transaction) : InvMenuTransactionResult{
		$slot = $transaction->getAction()->getSlot();
		$in = $transaction->getIn();
		$out = $transaction->getOut();

		if($slot === $this->ingredientSlot && !$in->isNull() && $out->isNull()){
			$this->tile->lastIngredientUnix = time();

			if($in->getCount() > 1){
				Utils::addItem($transaction->getPlayer(), $in->setCount($in->getCount() - 1));
			}

			$in->setCount(1);

			$this->tile->getInventory()->setItem(0, $in);
			return $transaction->continue();
		}

		if($slot === $this->ingredientSlot && !$out->isNull() && $in->isNull()){
			$this->tile->getInventory()->setItem(0, VanillaItems::AIR());
			return $transaction->continue();
		}

		if(in_array($slot, $this->slots)){
			if($out->getId() === ItemIds::POTION && $in->isNull()){
				$this->tile->getInventory()->setItem($this->getCoresspondingSlot($slot), VanillaItems::AIR());

				return $transaction->continue();
			}

			if($in->getId() === ItemIds::POTION) {
				$this->tile->getInventory()->setItem($this->getCoresspondingSlot($slot), $in);
				return $transaction->continue();
			}
		}

		return $transaction->discard();
	}


	public function update(Inventory $inventory): void {
		$inventory->setItem($this->ingredientSlot, $this->tile->getInventory()->getItem(0));

		for($i = 1; $i <= 3; $i++){
			$inventory->setItem($this->slots[$i-1], $this->tile->getInventory()->getItem($i));
		}
	}
}