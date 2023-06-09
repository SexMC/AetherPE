<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\server;

use Closure;
use pocketmine\Server;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class ServerMessagePacket extends BasePacket{

	public function __construct(public string $message, public array $receivers, public array $excludedServers = [], Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		parent::handle($data);

		if(empty($data["receivers"])){
			Server::getInstance()->broadcastMessage($data["message"]);
			return;
		}

		foreach($data["receivers"] as $receiver){
			if(($p = Server::getInstance()->getPlayerExact($receiver))){
				$p->sendMessage($data["message"]);
			}
		}
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["message"] = $this->message;
		$r["receivers"] = $this->receivers;
		$r["excludedServers"] = $this->excludedServers;

		return $r;
	}

	public static function create(array $data) : static{
		return new self($data["message"], $data["receivers"], $data["excludedServers"]);
	}

	public function getType() : int{
		return PacketIds::MESSAGE;
	}
}