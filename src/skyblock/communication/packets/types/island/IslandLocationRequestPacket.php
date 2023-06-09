<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\island;

use Closure;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class IslandLocationRequestPacket extends BasePacket{

	public function __construct(public string $island, Closure $closure = null){
		parent::__construct($closure);
	}

	public static function create(array $data) : static{
		$pk = new self($data["island"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["island"] = $this->island;

		return $r;
	}

	public function getType() : int{
		return PacketIds::REQUEST_ISLAND_LOCATION;
	}
}