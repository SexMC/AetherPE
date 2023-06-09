<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\auctionhouse;

use Closure;
use pocketmine\item\Steak;
use pocketmine\item\Tool;
use pocketmine\item\ToolTier;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;
use skyblock\misc\auctionhouse\AuctionHouseItem;

class AuctionAddRequestPacket extends BasePacket{

	public function __construct(public AuctionHouseItem $auctionHouseItem, Closure $closure = null){
		parent::__construct($closure);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["item"] = $this->auctionHouseItem;

		return $r;
	}

	public function getType() : int{
		return PacketIds::AUCTION_ADD_REQUEST;
	}
}