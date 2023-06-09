<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\island;

use Closure;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class IslandLoadResponsePacket extends BasePacket{

	public function __construct(public string $island, public string $loadedServer, public string $needsBack, Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		if($this->callbackID !== ""){
			ClosureStorage::executeClosure($this->callbackID, $this);
		}
	}

	public static function create(array $data) : static{
		$pk = new self($data["island"], $data["loadedServer"], $data["needsBack"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["island"] = $this->island;
		$r["loadedServer"] = $this->loadedServer;
		$r["needsBack"] = $this->needsBack;

		return $r;
	}

	public function getType() : int{
		return PacketIds::LOAD_ISLAND_RESPONSE;
	}
}