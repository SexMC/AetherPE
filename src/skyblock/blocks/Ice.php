<?php

declare(strict_types=1);

namespace skyblock\blocks;

use pocketmine\block\BlockBreakInfo;
use pocketmine\block\BlockIdentifier;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\player\Player;

class Ice extends \pocketmine\block\Ice {

    public function __construct() {
        parent::__construct(new BlockIdentifier(BlockLegacyIds::ICE, 0), "Ice", new BlockBreakInfo(0.5, BlockToolType::PICKAXE));
    }

    public function onBreak(Item $item, ?Player $player = null) : bool{
        if(($player === null || $player->isSurvival()) && !$item->hasEnchantment(VanillaEnchantments::SILK_TOUCH())){
            $this->position->getWorld()->setBlock($this->position, new Water());
            return true;
        }
        return parent::onBreak($item, $player);
    }

}