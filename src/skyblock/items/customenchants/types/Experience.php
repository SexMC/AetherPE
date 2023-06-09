<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;

class Experience extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Grants a §a12.5%*(ce level)§r§7 chance for double xp drops.");
		$this->setApplicableTo(self::ITEM_PICKAXE);
		$this->setMaxLevel(5);
		$this->setEvents([BlockBreakEvent::class]);

		return new CustomEnchantIdentifier("experience", "Experience", false);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof BlockBreakEvent) return;

		$event->setXpDropAmount($event->getXpDropAmount() * 2);
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{

		return $event instanceof BlockBreakEvent && mt_rand(1, 100) <= 12 * $enchantInstance->getLevel();
	}
}