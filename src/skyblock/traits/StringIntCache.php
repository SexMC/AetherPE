<?php

declare(strict_types=1);

namespace skyblock\traits;

trait StringIntCache {

	/**
	 * @var array<string, int>
	 */
	private array $cache = [];


	public function set(string $key, int $value): void {
		$this->cache[strtolower($key)] = $value;
	}

	public function get(string $key): int {
		return $this->cache[strtolower($key)] ?? 0;
	}

	public function remove(string $key): void {
		unset($this->cache[strtolower($key)]);
	}

	public function exists(string $key): bool {
		return isset($this->cache[strtolower($key)]);
	}
}