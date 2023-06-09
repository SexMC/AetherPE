<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;
use skyblock\utils\PveUtils;

class BlastProtection extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::common());
		$this->setDescription("Grants §a+30 " . PveUtils::getDefense() . " per level §r§7against explosions");
		$this->setApplicableTo(self::ITEM_ARMOUR);
		$this->setMaxLevel(5);
		$this->setEvents([EntityDamageEvent::class]);

		return new CustomEnchantIdentifier("blast_protection", "Blast Protection", true);
	}

	//todo: this is not implemented yet
	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		return false;
	}
}