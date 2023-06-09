<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types\reward;

use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class QuestItemReward extends QuestReward {

	public function __construct(private Item $item, private int $count = 1){
		$this->item = $this->item->setCount($this->count);
	}

	public function give(Player $player) : void{
		$item = $this->item->setCount($this->count);
		(new Session($player))->addCollectItem($item);
	}



	public function getItem() : Item{
		return $this->item;
	}

	public function getCount() : int{
		return $this->count;
	}
}