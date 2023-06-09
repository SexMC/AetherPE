<?php

declare(strict_types=1);

namespace skyblock\caches\size;

use pocketmine\math\AxisAlignedBB;
use skyblock\traits\AetherSingletonTrait;
use skyblock\traits\StringIntCache;

class IslandBoundingBoxCache {
	use AetherSingletonTrait;


	/**
	 * @var array<string, AxisAlignedBB>
	 */
	private array $cache = [];


	public function set(string $key, AxisAlignedBB $value): void {
		$this->cache[strtolower($key)] = $value;
	}

	public function get(string $key): ?AxisAlignedBB {
		return $this->cache[strtolower($key)] ?? null;
	}

	public function remove(string $key): void {
		unset($this->cache[strtolower($key)]);
	}

	public function exists(string $key): bool {
		return isset($this->cache[strtolower($key)]);
	}
}