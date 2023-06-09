<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\coinflip;

use Closure;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class CoinflipRemoveRequestPacket extends BasePacket{

	public function __construct(public string $player, Closure $closure = null){
		parent::__construct($closure);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["player"] = $this->player;

		return $r;
	}

	public function getType() : int{
		return PacketIds::COINFLIP_REMOVE_REQUEST;
	}
}