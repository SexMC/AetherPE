<?php

declare(strict_types=1);

namespace skyblock\misc\shop;

use pocketmine\item\Item;

abstract class ShopCategory {

	private array $items = [];
	private Item $categoryItem;

	public function __construct(){
		foreach($this->buildItems() as $item){
			$this->items[] = $item;
		}

		$this->categoryItem = $this->buildCategoryItem();
	}

	/**
	 * @return Item
	 */
	public function getCategoryItem() : Item{
		return $this->categoryItem->setCustomName("§f§l{$this->getCategoryName()}")->setLore(["§7Click to view the {$this->getCategoryName()} category"]);
	}

	/**
	 * @return ShopItem[]
	 */
	public function getItems() : array{
		return $this->items;
	}

	public abstract function getCategoryName();

	public abstract function buildCategoryItem(): Item;

	/**
	 * @return ShopItem[]
	 */
	public abstract function buildItems(): array;
}