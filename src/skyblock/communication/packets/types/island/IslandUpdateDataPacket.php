<?php

declare(strict_types=1);

namespace skyblock\communication\packets\types\island;

use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketIds;
use skyblock\islands\Island;

class IslandUpdateDataPacket extends BasePacket{

	const UPDATE_BOUNDING_BOX = "bb";

	public function __construct(public string $island, public string $data){
		parent::__construct();
	}

	public function handle(array $data) : void{
		parent::handle($data);

		$island = new Island($this->island);
		if(!$island->exists()) return;


		switch($this->data){
			case self::UPDATE_BOUNDING_BOX:
				$island->updateBoundingBoxCache();
				break;
		}
	}

	public function jsonSerialize(){
		$r = parent::jsonSerialize();
		$r["island"] = $this->island;
		$r["data"] = $this->data;

		return $r;
	}

	public static function create(array $data) : static{
		return new self($data["island"], $data["data"]);
	}

	public function getType() : int{
		return PacketIds::ISLAND_UPDATE_DATA;
	}
}