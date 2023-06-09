<?php

declare(strict_types=1);

namespace skyblock\misc\pve;

use JsonSerializable;

class CombatMob implements JsonSerializable{

	public function __construct(
		private string $entityID,
		private int $health,
		private bool $hostile,
		private float $speed,
		private float $damage = 0, //damage it deals to players
	){ }


	public static function fromJson(array $data): self {
		return new self(
			$data[0],
			$data[1],
			$data[2],
			$data[3],
			$data[4],
		);
	}

	public function jsonSerialize(){
		return [
			$this->entityID,
			$this->health,
			$this->hostile,
			$this->speed,
			$this->damage,
		];
	}
}