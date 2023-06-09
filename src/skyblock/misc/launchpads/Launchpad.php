<?php

declare(strict_types=1);

namespace skyblock\misc\launchpads;

use JsonSerializable;
use pocketmine\math\Vector3;
use skyblock\caches\pvpzones\PvpZone;

class Launchpad implements JsonSerializable {

	public function __construct(public string $name, public Vector3 $from, public Vector3 $to){

	}

	public static function fromJson(array $data): self {


		return new self(
			$data["name"],
			new Vector3($data["from"]["x"], $data["from"]["y"], $data["from"]["z"]),
			new Vector3($data["to"]["x"], $data["to"]["y"], $data["to"]["z"])
		);
	}

	public function jsonSerialize(){
		return [
			"name" => $this->name,
			"from" => PvpZone::jsonSerializeVector($this->from),
			"to" => PvpZone::jsonSerializeVector($this->to),
		];
	}
}