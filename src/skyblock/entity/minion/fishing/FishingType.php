<?php

declare(strict_types=1);

namespace skyblock\entity\minion\fishing;

use pocketmine\block\Block;
use pocketmine\item\Item;

class FishingType {

	public function __construct(private string $name, private Item $item){ }

	public function getName() : string{
		return $this->name;
	}


	public function getItem() : Item{
		return $this->item;
	}
}