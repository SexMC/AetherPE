<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\mechanics\item;

use Closure;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;

class ItemResponsePacket extends BasePacket{

	public function __construct(public string $player, public Item $item, Closure $closure = null){ parent::__construct($closure); }


	public function handle(array $data) : void{
		ClosureStorage::executeClosure($this->callbackID, $this);
	}

	public static function create(array $data) : static{
		if($data["data"]["player"] === "not_found"){
			$pk = new self($data["data"]["player"], VanillaItems::AIR());
			$pk->callbackID = $data["callbackID"];
			return $pk;
		}

		$pk = new self(
			$data["data"]["player"],
			Item::jsonDeserialize($data["data"]["item"]),
		);

		$pk->callbackID = $data["callbackID"];

		return $pk;
	}

	public function getType() : int{
		return PacketIds::RESPONSE_ITEM;
	}
}