<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\player;

use Closure;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class FindPlayerLocationResponsePacket extends BasePacket{

	public function __construct(public string $player, public string $server, Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		parent::handle($data);

		if(isset($data["callbackID"])){
			ClosureStorage::executeClosure($data["callbackID"], $this);
		}
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["player"] = $this->player;
		$r["server"] = $this->server;

		return $r;
	}

	public static function create(array $data) : static{
		$pk = new self($data["player"], $data["server"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function getType() : int{
		return PacketIds::PLAYER_LOCATION_RESPONSE;
	}
}