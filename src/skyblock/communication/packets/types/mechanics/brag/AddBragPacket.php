<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\brag;

use Closure;
use pocketmine\item\Item;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class AddBragPacket extends BasePacket{

	public array $inventory = [];
	public array $armorInventory = [];

	public function __construct(public string $player, array $inventory, array $armorInventory, Closure $closure = null){
		$this->inventory = array_map(fn(Item $item) => $item->jsonSerialize(), $inventory);
		$this->armorInventory = array_map(fn(Item $item) => $item->jsonSerialize(), $armorInventory);

		parent::__construct($closure);
	}


	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["brag"] = [
			"player" => $this->player,
			"inventory" => $this->inventory,
			"armorInventory" => $this->armorInventory
		];


		return $r;
	}

	public function getType() : int{
		return PacketIds::ADD_BRAG;
	}
}