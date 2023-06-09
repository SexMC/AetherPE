<?php

declare(strict_types=1);

namespace skyblock\entity\object;

use pocketmine\entity\object\FallingBlock;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\world\particle\HugeExplodeParticle;

class EnvoyEntity extends FallingBlock {

	private int $cd = 0;

	private ?int $rarity = null;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setCanSaveWithChunk(false);
	}

	protected function entityBaseTick(int $tickDiff = 1) : bool{
		$parent = parent::entityBaseTick($tickDiff);

		if(++$this->cd >= 3){
			$this->cd = 0;

			$this->getPosition()->getWorld()->addParticle($this->getPosition(), new HugeExplodeParticle());
		}


		return $parent;
	}


	/**
	 * @return int|null
	 */
	public function getRarity() : ?int{
		return $this->rarity;
	}

	/**
	 * @param int|null $rarity
	 */
	public function setRarity(?int $rarity) : void{
		$this->rarity = $rarity;
	}
}