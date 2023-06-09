<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\auctionhouse;

use Closure;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class AuctionRemoveRequestPacket extends BasePacket{

	public function __construct(public string $auctionID, Closure $closure = null){
		parent::__construct($closure);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["auctionID"] = $this->auctionID;

		return $r;
	}

	public function getType() : int{
		return PacketIds::AUCTION_REMOVE_REQUEST;
	}
}