<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\island;

use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class IslandSetUnloadingPacket extends BasePacket{

	public function __construct(public string $island, public bool $state){
		parent::__construct(null);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["island"] = $this->island;
		$r["state"] = $this->state;

		return $r;
	}

	public function getType() : int{
		return PacketIds::ISLAND_SET_UNLOADING;
	}
}