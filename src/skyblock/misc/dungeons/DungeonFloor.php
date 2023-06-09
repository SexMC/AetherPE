<?php

declare(strict_types=1);

namespace skyblock\misc\dungeons;

use Generator;

abstract class DungeonFloor {

	private array $puzzles = [];

	public function __construct(){
		$this->setup();
	}

	public abstract function setup(): void;
	public abstract function start(DungeonInstance $instance): Generator;

	public function addPuzzle(DungeonPuzzle $puzzle): void {
		$this->puzzles[] = $puzzle;
	}

	public function getPuzzle(int $index): ?DungeonPuzzle {
		return $this->puzzles[$index] ?? null;
	}

}