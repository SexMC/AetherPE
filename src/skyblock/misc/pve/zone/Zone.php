<?php

declare(strict_types=1);

namespace skyblock\misc\pve\zone;

use JsonSerializable;
use pocketmine\math\AxisAlignedBB;

abstract class Zone implements JsonSerializable{

	public function __construct(
		protected string $type,
		protected string $name,
		protected AxisAlignedBB $bb,
	){}

	public function start(): void {}
	public function stop(): void {}

	public function jsonSerialize(){
		return [
			"type" => $this->type,
			"name" => $this->name,
			"aabb" => $this->AABBToJson(),
		];
	}

	public static abstract function fromJson(array $data): self;


	protected function AABBToJson(): array {
		return [
			"minX" => $this->bb->minX,
			"minY" => $this->bb->minY,
			"minZ" => $this->bb->minZ,

			"maxX" => $this->bb->maxX,
			"maxY" => $this->bb->maxY,
			"maxZ" => $this->bb->maxZ,
		];
	}

	/**
	 * @return string
	 */
	public function getType() : string{
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return AxisAlignedBB
	 */
	public function getBb() : AxisAlignedBB{
		return $this->bb;
	}
}