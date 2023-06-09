<?php

declare(strict_types=1);

namespace skyblock\misc\quests\types;

use Closure;
use pocketmine\block\Block;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\Crops;
use skyblock\misc\quests\Quest;

class BlockBreakQuest extends Quest {

	private ?Block $second = null;
	private ?string $worldName = null;

	public function __construct(private Block $block, int $goal, string $name, string $objective, string $rewardsText, array $rewards = [], ?Closure $onFinish = null){ parent::__construct($goal, $name, $objective, $rewardsText, $rewards, $onFinish); }

	public function getType() : int{
		return self::MINE_BLOCK;
	}

	public function setSecond(Block $block): void {
		$this->second = $block;
	}

	public function setWorldName(?string $worldName) : void{
		$this->worldName = $worldName;
	}

	/**
	 * @param Block $object
	 *
	 * @return int
	 */
	public function shouldIncreaseProgress($object) : int{
		if($this->worldName !== null){
			if(strtolower($object->getPosition()->getWorld()->getDisplayName()) !== strtolower($this->worldName)){
				return 0;
			}
		}

		if($this->block->getId() === BlockLegacyIds::AIR) {
			return 1;
		}

		if($object instanceof Crops && $object->getId() === $this->block->getId()){
			if($object->getAge() >= 7){
				return 1;
			}

			return 0;
		}

		if($this->second !== null){
			if($object instanceof Block && $object->getId() === $this->second->getId() && $object->getMeta() === $this->second->getMeta()){
				return 1;
			}
		}

		if($object instanceof Block && $object->getId() === $this->block->getId() && $object->getMeta() === $this->block->getMeta()){
			return 1;
		}

		return 0;
	}
}