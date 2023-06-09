<?php

declare(strict_types=1);

namespace skyblock\sessions;

use pocketmine\item\Item;
use skyblock\items\accessory\Accessory;
use skyblock\items\accessory\AccessoryItem;

trait SessionAccessoryData {

	/**
	 * @return AccessoryItem[]
	 */
	public function getAccessories(): array {
		$raw = $this->getRedis()->get("player.{$this->username}.accessory.collection");

		if($raw !== null){
			$arr = json_decode($raw, true);
		} else $arr = [];


		$r = [];
		foreach($arr as $v){
			$accessory = Item::jsonDeserialize($v);

			if($accessory instanceof AccessoryItem){
				$r[$accessory->getAccessoryName()] = $accessory->setCount(1);
			}
		}

		return $r;
	}

	public function setAccessories(array $accessories) : void{
		$this->getRedis()->set("player.{$this->username}.accessory.collection", json_encode($accessories));
	}

	public function getExtraAccessorySlots(): int {
		return (int) ($this->getRedis()->get("player.{$this->username}.accessory.size") ?? 0);
	}

	public function setExtraAccessorySlots(int $extraSlots) : void{
		$this->getRedis()->set("player.{$this->username}.accessory.size", $extraSlots);
	}
}