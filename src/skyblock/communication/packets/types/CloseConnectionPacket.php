<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types;

use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class CloseConnectionPacket extends BasePacket{

	public function getType() : int{
		return PacketIds::CLOSE_CONNECTION;
	}
}