<?php

declare(strict_types=1);

namespace skyblock\caches\block;

use pocketmine\block\Block;
use pocketmine\scheduler\TaskHandler;
use skyblock\blocks\ITickableBlock;
use skyblock\traits\AetherSingletonTrait;
use skyblock\utils\Utils;

class TickableBlockSchedulerCache {
	use AetherSingletonTrait;

	/** @var array<string, TaskHandler> */
	private array $cache = [];

	public function schedule(Block $block, int $delay): void {
		$this->cache[$this->getBlockIdentifier($block)] = Utils::executeLater(function() use($block) : void{
			$w = $block->getPosition()->world;
			if($w === null) return;
			if(!$w->isLoaded()) return;

			if($block instanceof ITickableBlock){
				if($block->getPosition()->getWorld()->getBlock($block->getPosition()) instanceof ITickableBlock){
					$block->onUpdate();
				}
			}
		}, $delay);
	}

	public function cancel(Block $block): void {
		$id = $this->getBlockIdentifier($block);

		if(isset($this->cache[$id])){
			$this->cache[$id]->cancel();
			unset($this->cache[$id]);
		}
	}

	public function contains(Block $block): bool {
		return isset($this->cache[$this->getBlockIdentifier($block)]);
	}

	public function getBlockIdentifier(Block $block): string {
		return $block->getPosition()->getWorld()->getDisplayName() . $block->getPosition()->getFloorX() . $block->getPosition()->getFloorY() . $block->getPosition()->getFloorZ();
	}
}