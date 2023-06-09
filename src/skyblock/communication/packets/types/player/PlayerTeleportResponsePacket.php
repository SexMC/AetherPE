<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\player;

use Closure;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class PlayerTeleportResponsePacket extends BasePacket{


	public function __construct(public string $server, public string $needsBack, Closure $closure = null){
		parent::__construct($closure);
	}

	public static function create(array $data) : static{
		$pk = new self($data["server"], $data["needsBack"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function handle(array $data) : void{
		if($this->callbackID !== ""){
			ClosureStorage::executeClosure($this->callbackID, $this);
		}
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["server"] = $this->server;
		$r["needsBack"] = $this->needsBack;

		return $r;
	}

	public function getType() : int{
		return PacketIds::PLAYER_TELEPORT_RESPONSE;
	}
}