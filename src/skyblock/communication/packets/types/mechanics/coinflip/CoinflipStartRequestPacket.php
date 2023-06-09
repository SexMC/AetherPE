<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\coinflip;

use Closure;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class CoinflipStartRequestPacket extends BasePacket{

	public function __construct(public string $owner, public string $player, public string $color, Closure $closure = null){
		parent::__construct($closure);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["player"] = $this->player;
		$r["owner"] = $this->owner;
		$r["color"] = $this->color;

		return $r;
	}

	public function getType() : int{
		return PacketIds::COINFLIP_START_REQUEST;
	}
}