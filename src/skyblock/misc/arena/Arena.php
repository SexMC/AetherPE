<?php

declare(strict_types=1);

namespace skyblock\misc\arena;

use Closure;
use pocketmine\world\World;

class Arena {

	public bool $isActive = true;

	private array $data = [];

	public function __construct(
		public string $id,
		public World $world,
		public Closure $onLaunch,
		public Closure $onFinish,
		public ?Closure $onTick,
		public ?Closure $onWorldEnter,
		public ?Closure $onWorldExit,
		public int $createdUnix
	){ }

	public function store(string $key, $data): void {
		$this->data[$key] = $data;
	}

	public function fetch(string $key) {
		return $this->data[$key] ?? null;
	}
}