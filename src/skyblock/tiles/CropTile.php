<?php

declare(strict_types=1);

namespace skyblock\tiles;

use pocketmine\block\tile\Tile;
use pocketmine\nbt\tag\CompoundTag;

class CropTile extends Tile {
    private bool $placed = false;

    public function isPlaced(): bool {
        return $this->placed;
    }

    public function setPlaced(bool $placed): void {
        $this->placed = $placed;
    }

    public function readSaveData(CompoundTag $nbt): void {
        $this->placed = (bool) $nbt->getByte('placed', 0);
    }

    protected function writeSaveData(CompoundTag $nbt): void {
        $nbt->setByte('placed', (int) $this->placed);
    }
}