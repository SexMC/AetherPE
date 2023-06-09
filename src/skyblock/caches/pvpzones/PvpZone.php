<?php

declare(strict_types=1);

namespace skyblock\caches\pvpzones;

use JsonSerializable;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class PvpZone implements JsonSerializable {

	public AxisAlignedBB $bb;

	public function __construct(
		public string $name,
		public string $world,
		public Vector3 $pos1,
		public Vector3 $pos2
	){
		$this->bb = new AxisAlignedBB(
			min($this->pos1->getX(), $this->pos2->getX()),
			min($this->pos1->getY(), $this->pos2->getY()),
			min($this->pos1->getZ(), $this->pos2->getZ()),
			max($this->pos1->getX(), $this->pos2->getX()),
			max($this->pos1->getY(), $this->pos2->getY()),
			max($this->pos1->getZ(), $this->pos2->getZ()),
		);
	}

	public static function fromJson(array $data): PvpZone {
		return new PvpZone(
			$data["name"],
			$data["world"],
			new Vector3($data["pos1"]["x"], $data["pos1"]["y"], $data["pos1"]["z"]),
			new Vector3($data["pos2"]["x"], $data["pos2"]["y"], $data["pos2"]["z"]),
		);
	}

	public function jsonSerialize(){
		return [
			"name" => $this->name,
			"world" => $this->world,
			"pos1" => self::jsonSerializeVector($this->pos1),
			"pos2" => self::jsonSerializeVector($this->pos2),
		];
	}

	public static function jsonSerializeVector(Vector3 $vector3): array {
		return ["x" => $vector3->getFloorX(), "y" => $vector3->getFloorY(), "z" => $vector3->getFloorZ()];
	}
}