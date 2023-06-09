<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\player;

use Closure;
use skyblock\communication\CommunicationData;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;
use skyblock\Main;
use skyblock\utils\Utils;

class PlayerTeleportRequestPacket extends BasePacket{

	const MODE_ISLAND_WARP = 1;
	const MODE_WARP = 2;
	const MODE_PLAYER = 3;

	public function __construct(public string $player, public int $mode, public string $data, public string $needsBack, Closure $closure = null){
		parent::__construct($closure);
	}

	public static function create(array $data) : static{
		$pk = new self($data["player"], $data["mode"], $data["data"], $data["needsBack"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function handle(array $data) : void{
		if($this->mode === self::MODE_PLAYER){
			CommunicationData::setTeleportingData($this->player, ["unix" => time(), "targetPlayer" => $this->data]);
		}

		if($this->mode === self::MODE_WARP){
			CommunicationData::setTeleportingData($this->player, ["unix" => time(), "warpName" => $this->data]);
		}

		if($this->mode === self::MODE_ISLAND_WARP){
			CommunicationData::setWarpingData($this->player, $this->data);
		}

		$pk = new PlayerTeleportResponsePacket(Utils::getServerName(), $this->needsBack);
		$pk->callbackID = $this->callbackID;
		Main::getInstance()->getCommunicationLogicHandler()->sendPacket($pk);
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["mode"] = $this->mode;
		$r["player"] = $this->player;
		$r["data"] = $this->data;
		$r["needsBack"] = $this->needsBack;

		return $r;
	}

	public function getType() : int{
		return PacketIds::PLAYER_TELEPORT_REQUEST;
	}
}