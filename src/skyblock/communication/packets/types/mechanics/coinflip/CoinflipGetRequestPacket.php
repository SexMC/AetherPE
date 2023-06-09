<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\coinflip;

use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class CoinflipGetRequestPacket extends BasePacket{

	public function getType() : int{
		return PacketIds::COINFLIP_GETALL_REQUEST;
	}
}