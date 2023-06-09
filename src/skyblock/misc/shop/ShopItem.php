<?php

declare(strict_types=1);

namespace skyblock\misc\shop;

use pocketmine\item\Item;

class ShopItem {

	private string $id;

	/**
	 * @return Item
	 */
	public function getItem() : Item{
		return $this->item->setCount(1);
	}

	public function getViewItem(): Item {
		$item = clone $this->item;

		$item->setCustomName("§f§l" . $item->getName());
		$item->setLore(array_merge($item->getLore(), ["", "§l§6Buy price: §6$" . number_format($this->buyPrice)]));

		$item->getNamedTag()->setString("shopItem", $this->getId());

		return $item;
	}

	/**
	 * @return int
	 */
	public function getBuyPrice() : int{
		return $this->buyPrice;
	}

	/**
	 * @return int
	 */
	public function getSellPrice() : int{
		return $this->sellPrice;
	}

	public function __construct(private Item $item, private int $buyPrice, private int $sellPrice = 0){
		$this->id = spl_object_hash($this);
	}

	/**
	 * @return string
	 */
	public function getId() : string{
		return $this->id;
	}
}