<?php

declare(strict_types=1);

namespace skyblock\misc\pve;

use JsonSerializable;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class PveEntityEquipment implements JsonSerializable{

	public function __construct(
		public ?Item $hand,
		public ?Item $helmet = null,
		public ?Item $chestplate = null,
		public ?Item $leggings = null,
		public ?Item $boots = null,
	){

		if($this->hand === null){
			$this->hand = VanillaItems::AIR();
		}

		if($this->chestplate === null){
			$this->chestplate = VanillaItems::AIR();
		}


		if($this->boots === null){
			$this->boots = VanillaItems::AIR();
		}
		if($this->helmet === null){
			$this->helmet = VanillaItems::AIR();
		}

		if($this->leggings === null){
			$this->leggings = VanillaItems::AIR();
		}

	}

	public function jsonSerialize(){
		return [
			$this->hand,
			$this->helmet,
			$this->chestplate,
			$this->leggings,
			$this->boots
		];
	}

	public static function fromJson(array $data): self {
		return new self(
			Item::jsonDeserialize($data[0]),
			Item::jsonDeserialize($data[1]),
			Item::jsonDeserialize($data[2]),
			Item::jsonDeserialize($data[3]),
			Item::jsonDeserialize($data[4]),
		);
	}
}