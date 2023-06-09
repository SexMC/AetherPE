<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class Autosmelt extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Chance to automatically smelts broken blocks into their smelted form");
		$this->setApplicableTo(self::ITEM_PICKAXE);
		$this->setMaxLevel(4);
		$this->setEvents([BlockBreakEvent::class]);

		return new CustomEnchantIdentifier("auto_smelt", "Auto Smelt", false);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance): void {
		if ($event instanceof BlockBreakEvent) {
			$newDrops = $event->getDrops();
			foreach ($newDrops as $k => $drop) {

				$newId = match ($drop->getId()) {
					ItemIds::IRON_ORE => ItemIds::IRON_INGOT,
					ItemIds::GOLD_ORE => ItemIds::GOLD_INGOT,
					ItemIds::COBBLESTONE => ItemIds::STONE,
					default => 0
				};

				if ($newId !== 0) {
					$newDrops[$k] = ItemFactory::getInstance()->get($newId, 0, $drop->getCount());
				}
			}

			$event->setDrops($newDrops);
		}
	}


	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance): bool {
		return $event instanceof BlockBreakEvent && mt_rand(1, 100) <= $enchantInstance->getLevel() * 25;
	}
}