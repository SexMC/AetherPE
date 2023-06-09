<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\player;

use Closure;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class FindPlayerLocationRequestPacket extends BasePacket{

	public function __construct(public string $player, Closure $closure = null){
		parent::__construct($closure);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["player"] = $this->player;

		return $r;
	}

	public function getType() : int{
		return PacketIds::PLAYER_LOCATION_REQUEST;
	}
}