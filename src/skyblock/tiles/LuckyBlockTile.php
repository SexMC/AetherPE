<?php

declare(strict_types=1);

namespace skyblock\tiles;

use pocketmine\block\tile\Tile;
use pocketmine\nbt\NbtDataException;
use pocketmine\nbt\tag\CompoundTag;

class LuckyBlockTile extends Tile {
	public function readSaveData(CompoundTag $nbt) : void{}

	protected function writeSaveData(CompoundTag $nbt) : void{}
}