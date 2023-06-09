<?php

declare(strict_types=1);

namespace skyblock\traits;

trait StringStringCache {

	/**
	 * @var array<string, string>
	 */
	private array $cache = [];


	public function set(string $key, ?string $value): void {
		$this->cache[strtolower($key)] = $value;
	}

	public function get(string $key): ?string {
		return $this->cache[strtolower($key)] ?? null;
	}

	public function remove(string $key): void {
		unset($this->cache[strtolower($key)]);
	}

	public function exists(string $key): bool {
		return isset($this->cache[strtolower($key)]);
	}
}