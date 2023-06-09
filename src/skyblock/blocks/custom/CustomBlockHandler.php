<?php

declare(strict_types=1);

namespace skyblock\blocks\custom;

use pocketmine\event\Event;
use pocketmine\world\Position;
use skyblock\blocks\custom\types\CropTotemCustomBlock;
use skyblock\blocks\custom\types\FarmCrystalCustomBlock;
use skyblock\blocks\custom\types\GalaxyTotemCustomBlock;
use skyblock\blocks\custom\types\LuckyTotemCustomBlock;
use skyblock\traits\AetherHandlerTrait;

class CustomBlockHandler {

	private array $blocks = [];

	use AetherHandlerTrait;

	public function onEnable() : void{
		$this->register(new GalaxyTotemCustomBlock());
		$this->register(new CropTotemCustomBlock());
		$this->register(new LuckyTotemCustomBlock());
		$this->register(new FarmCrystalCustomBlock());
	}

	public function register(CustomBlock $block){
		$this->blocks[strtolower($block->getIdentifier())] = $block;
	}

	public function getBlock(string $id): ?CustomBlock {
		return $this->blocks[strtolower($id)] ?? null;
	}

	/**
	 * @return array
	 */
	public function getAllBlocks() : array{
		return $this->blocks;
	}

	public static function attemptCall(Position $pos, Event $event) : void {
		$chunk = $pos->getWorld()->getChunk($pos->x >> 4, $pos->z >> 4);
		foreach ($chunk->getTiles() as $tile) {
			if (!$tile instanceof CustomBlockTile) {
				continue;
			}

			$sb = $tile->getSpecialBlock();
			if (!$sb->hasEvent($event)) {
				continue;
			}

			$sb->onEvent($tile, $event);
		}
	}
}