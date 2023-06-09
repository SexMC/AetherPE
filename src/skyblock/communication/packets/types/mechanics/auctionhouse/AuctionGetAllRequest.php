<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\auctionhouse;

use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class AuctionGetAllRequest extends BasePacket{

	public function getType() : int{
		return PacketIds::AUCTION_GETALL_REQUEST;
	}
}