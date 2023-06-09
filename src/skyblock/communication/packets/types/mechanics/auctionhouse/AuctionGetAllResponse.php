<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\auctionhouse;

use Closure;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class AuctionGetAllResponse extends BasePacket{

	public function __construct(public array $items, Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		if($this->callbackID !== ""){
			ClosureStorage::executeClosure($this->callbackID, $this->items);
		}
	}

	public static function create(array $data) : static{
		$r = new self($data["items"]);
		$r->callbackID = $data["callbackID"];

		return $r;
	}

	public function getType() : int{
		return PacketIds::AUCTION_GETALL_RESPONSE;
	}
}