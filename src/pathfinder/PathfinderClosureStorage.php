<?php

declare(strict_types=1);

namespace pathfinder;

use Closure;
use skyblock\traits\AetherHandlerTrait;

class PathfinderClosureStorage {
	use AetherHandlerTrait;


	private array $closures = [];


	public function addClosure(Closure $closure): string {
		$id = uniqid();
		$this->closures[$id] = $closure;
		return $id;
	}

	public function executeClosure(string $id, $data): void {
		$closure = $this->closures[$id] ?? null;
		if($closure === null) {
			var_dump("tried to execute null closure");
			return;
		};

		$closure($data);

		unset($this->closures[$id]);
	}
}