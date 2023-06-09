<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\coinflip;

use Closure;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class CoinflipGetResponsePacket extends BasePacket{

	public function __construct(public array $coinflips, Closure $closure = null){ parent::__construct($closure); }

	public function handle(array $data) : void{
		if($this->callbackID !== ""){
			ClosureStorage::executeClosure($this->callbackID, $this->coinflips);
		}
	}

	public static function create(array $data) : static{
		$pk = new self($data["coinflips"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function getType() : int{
		return PacketIds::COINFLIP_GETALL_RESPONSE;
	}
}