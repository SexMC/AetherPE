<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\server;

use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class PlayerListRequestPacket extends BasePacket{
	public function getType() : int{
		return PacketIds::PLAYER_LIST_REQUEST;
	}
}