<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\coinflip;

use Closure;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class CoinflipStartResponsePacket extends BasePacket{

	public function __construct(public bool $success, Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		if($this->callbackID !== ""){
			ClosureStorage::executeClosure($this->callbackID, $this->success);
		}
	}

	public static function create(array $data) : static{
		$pk = new self($data["success"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["success"] = $this->success;

		return $r;
	}

	public function getType() : int{
		return PacketIds::COINFLIP_REMOVE_RESPONSE;
	}
}