<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\coinflip;

use Closure;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;
use skyblock\misc\coinflip\Coinflip;

class CoinflipAddRequestPacket extends BasePacket{

	public function __construct(public Coinflip $coinflip, Closure $closure = null){
		parent::__construct($closure);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["coinflip"] = $this->coinflip;

		return $r;
	}

	public function getType() : int{
		return PacketIds::COINFLIP_ADD_REQUEST;
	}
}