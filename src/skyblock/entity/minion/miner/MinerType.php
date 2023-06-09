<?php

declare(strict_types=1);

namespace skyblock\entity\minion\miner;

use pocketmine\block\Block;

class MinerType {

	public function __construct(private string $name, private Block $block){ }

	public function getName() : string{
		return $this->name;
	}

	public function getBlock() : Block{
		return $this->block;
	}

}