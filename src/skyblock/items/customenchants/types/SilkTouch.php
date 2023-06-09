<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class SilkTouch extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Allows you to collect normally unobtainable blocks.");
		$this->setApplicableTo(self::ITEM_TOOLS);
		$this->setMaxLevel(1);
		$this->setEvents([BlockBreakEvent::class]);

		return new CustomEnchantIdentifier("silk_touch", "Silk Touch", false);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance): void {
		if ($event instanceof BlockBreakEvent) {
			$event->setDrops($this->getDrops($event->getBlock(), $event->getItem()));
		}
	}



	/**
	 * Returns an array of Item objects to be dropped
	 *
	 * @return Item[]
	 */
	private function getDrops(Block $block, Item $item) : array{
		if($block->getBreakInfo()->isToolCompatible($item)){
			if($block->isAffectedBySilkTouch()){
				return $block->getSilkTouchDrops($item);
			}

			return $block->getDropsForCompatibleTool($item);
		}

		return $block->getDropsForIncompatibleTool($item);
	}


	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance): bool {
		return $event instanceof BlockBreakEvent;
	}
}