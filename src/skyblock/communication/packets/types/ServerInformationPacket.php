<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types;

use skyblock\communication\datatypes\ServerInformation;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class ServerInformationPacket extends BasePacket{

	public function __construct(public ServerInformation $information){ }

	public function jsonSerialize(){
		$a = parent::jsonSerialize();
		$a["information"] = $this->information;

		return $a;
	}

	public function getType() : int{
		return PacketIds::UPDATE_STATUS;
	}
}