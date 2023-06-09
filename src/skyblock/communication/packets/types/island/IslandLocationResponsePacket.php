<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\island;

use Closure;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class IslandLocationResponsePacket extends BasePacket{

	public function __construct(public string $location, Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		if($this->callbackID !== ""){
			ClosureStorage::executeClosure($this->callbackID, $this);
		}
	}

	public static function create(array $data) : static{
		$pk = new self($data["location"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["location"] = $this->location;

		return $r;
	}

	public function getType() : int{
		return PacketIds::RESPONSE_ISLAND_LOCATION;
	}
}