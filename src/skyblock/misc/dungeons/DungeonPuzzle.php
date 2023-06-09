<?php

declare(strict_types=1);

namespace skyblock\misc\dungeons;

use Generator;

abstract class DungeonPuzzle {

	public function __construct(){
		$this->setup();
	}

	protected bool $isActive = false;
	protected bool $done = false;

	abstract public function getName(): string;

	/**
	 * @param DungeonInstance $instance
	 *
	 * @return Generator<string> returns the player name that has solved the puzzle
	 */
	abstract public function start(DungeonInstance $instance): Generator;
}