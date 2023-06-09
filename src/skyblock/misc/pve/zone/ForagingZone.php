<?php

declare(strict_types=1);

namespace skyblock\misc\pve\zone;

use pocketmine\math\AxisAlignedBB;

class ForagingZone extends Zone {

	public function __construct(string $name, AxisAlignedBB $bb){
		$this->type = "foraging";
		$this->name = $name;
		$this->bb = $bb;
	}

	public static function fromJson(array $data) : Zone{
		return new self(
			$data["name"],
			new AxisAlignedBB(...$data["aabb"]),
		);
	}
}