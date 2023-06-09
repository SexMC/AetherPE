<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\brag;

use Closure;
use pocketmine\item\Item;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class BragResponsePacket extends BasePacket{

	public function __construct(public string $player, public array $inventory, public array $armorInventory, Closure $closure = null){ parent::__construct($closure); }


	public function handle(array $data) : void{
		ClosureStorage::executeClosure($this->callbackID, $this);
	}

	public static function create(array $data) : static{
		if($data["data"]["player"] === "not_found"){
			$pk = new self($data["data"]["player"], [], []);
			$pk->callbackID = $data["callbackID"];
			return $pk;
		}

		$pk = new self(
			$data["data"]["player"],
			array_map(fn(array $s) => Item::jsonDeserialize($s), $data["data"]["inventory"]),
			array_map(fn(array $s) => Item::jsonDeserialize($s), $data["data"]["armorInventory"])
		);

		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function getType() : int{
		return PacketIds::RESPONSE_BRAG;
	}
}