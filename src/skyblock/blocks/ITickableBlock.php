<?php

declare(strict_types=1);

namespace skyblock\blocks;

interface ITickableBlock {

	public function onUpdate(): void;
}