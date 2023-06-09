<?php

declare(strict_types=1);

namespace skyblock\blocks;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifierFlattened;
use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\Entity;

class Water extends \pocketmine\block\Water {

    public function __construct() {
        parent::__construct(new BlockIdentifierFlattened(BlockLegacyIds::WATER, [BlockLegacyIds::STILL_WATER], 0), "Water", BlockBreakInfo::indestructible(500));
    }

    public function onEntityInside(Entity $entity) : bool{
		//TODO: check if entity is a nether mob

        return parent::onEntityInside($entity);
    }

}