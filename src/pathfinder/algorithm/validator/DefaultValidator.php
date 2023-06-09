<?php

declare(strict_types=1);

namespace pathfinder\algorithm\validator;

use pathfinder\algorithm\Algorithm;
use pocketmine\block\Air;
use pocketmine\block\BaseRail;
use pocketmine\block\Block;
use pocketmine\block\Lava;
use pocketmine\block\Slab;
use pocketmine\block\Stair;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\ChunkRequestTask;
use function ceil;

class DefaultValidator extends Validator {
    public function isSafeToStandAt(Algorithm $algorithm, Vector3 $vector3): bool{
		$v = $vector3->subtract(0, 1, 0);

        $block = $algorithm->getChunkManager()->getBlockAt($v->getFloorX(), $v->getFloorY(), $v->getFloorZ());

        if(!$block->isSolid() && !$block instanceof Slab && !$block instanceof Stair && !$block instanceof Air) {
			return false;
		}
        $axisAlignedBB = $algorithm->getAxisAlignedBB();
        $height = ceil($axisAlignedBB->maxY - $axisAlignedBB->minY);
        for($y = 0; $y <= $height; $y++) {
			$v = $vector3->add(0, $y, 0);
			$stand = $algorithm->getChunkManager()->getBlockAt($v->getFloorX(), $v->getFloorY(), $v->getFloorZ());
            if(!$this->isBlockEmpty($stand)) {
				return false;
			}
        }
        return true;
    }

    protected function isBlockEmpty(Block $block): bool {
        return !$block->isSolid() && !$block instanceof BaseRail && !$block instanceof Lava;
    }
}