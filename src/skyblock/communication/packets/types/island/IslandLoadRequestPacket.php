<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\island;

use Closure;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;
use skyblock\islands\Island;
use skyblock\islands\IslandHandler;
use skyblock\Main;
use skyblock\utils\IslandUtils;
use skyblock\utils\ProfileUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class IslandLoadRequestPacket extends BasePacket{

	public function __construct(public string $island, public string $needsBack, Closure $closure = null){
		parent::__construct($closure);
	}

	public function handle(array $data) : void{
		if(!Utils::isIslandServer()) return;

		Await::f2c(function(){
			$result = yield ProfileUtils::loadProfile($this->island);

			$pk = new IslandLoadResponsePacket($this->island, Utils::getServerName(), $this->needsBack);
			$pk->callbackID = $this->callbackID;

			if($result === false){
				$pk->island = "error";
			}

			Main::getInstance()->getCommunicationLogicHandler()->sendPacket($pk);

			//(new Island($this->island))->onLoad();
		});
	}

	public static function create(array $data) : static{
		$pk = new self($data["island"], $data["needsBack"]);
		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["island"] = $this->island;
		$r["needsBack"] = $this->needsBack;

		return $r;
	}

	public function getType() : int{
		return PacketIds::LOAD_ISLAND;
	}
}