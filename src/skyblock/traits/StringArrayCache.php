<?php

declare(strict_types=1);

namespace skyblock\traits;

trait StringArrayCache {

	/**
	 * @var array<string, array>
	 */
	private array $cache = [];


	public function set(string $key, array $value, $sub = null): void {
		if($sub !== null){
			$this->cache[strtolower($key)][$sub] = $value;
		} else $this->cache[strtolower($key)] = $value;
		$this->cache[strtolower($key)] = $value;
	}

	public function add(string $key, $value, $sub = null): void {
		if($sub !== null){
			$this->cache[strtolower($key)][$sub] = $value;
		} else $this->cache[strtolower($key)][] = $value;
	}

	public function get(string $key): array {
		return $this->cache[strtolower($key)] ?? [];
	}

	public function remove(string $key, $sub = null): void {
		if($sub !== null){
			unset($this->cache[strtolower($key)][$sub]);
		} else unset($this->cache[strtolower($key)]);
	}

	public function exists(string $key): bool {
		return isset($this->cache[strtolower($key)]);
	}
}