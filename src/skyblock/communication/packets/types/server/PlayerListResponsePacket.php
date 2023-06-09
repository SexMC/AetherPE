<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\server;

use Closure;
use skyblock\communication\CommunicationData;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class PlayerListResponsePacket extends BasePacket{

	public function __construct(public array $players, Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		CommunicationData::updateOnlinePlayers($data["players"]);
	}

	public static function create(array $data) : static{
		return new self($data["players"]);
	}

	public function getType() : int{
		return PacketIds::PLAYER_LIST_RESPONSE;
	}
}