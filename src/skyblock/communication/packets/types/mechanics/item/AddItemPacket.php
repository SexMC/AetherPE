<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\item;

use Closure;
use pocketmine\item\Item;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class AddItemPacket extends BasePacket{

	public array $item;

	public function __construct(public string $player, Item $item, Closure $closure = null){
		$this->item = $item->jsonSerialize();
		parent::__construct($closure);
	}


	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["item"] = [
			"player" => $this->player,
			"item" => $this->item,
		];

		return $r;
	}

	public function getType() : int{
		return PacketIds::ADD_ITEM;
	}
}