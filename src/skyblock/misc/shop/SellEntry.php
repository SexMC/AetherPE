<?php

declare(strict_types=1);

namespace skyblock\misc\shop;

use pocketmine\item\Item;

class SellEntry {

	public function __construct(private Item $item, private int $price, private bool $mobDrop){ }

	/**
	 * @return Item
	 */
	public function getItem() : Item{
		return $this->item;
	}

	/**
	 * @return int
	 */
	public function getPrice() : int{
		return $this->price;
	}

	/**
	 * @return bool
	 */
	public function isMobDrop() : bool{
		return $this->mobDrop;
	}
}