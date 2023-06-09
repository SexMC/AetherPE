<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\event\Event;
use pocketmine\player\Player;
use skyblock\events\player\PlayerPreFishEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;
use skyblock\utils\Utils;

class Angler extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Increases chance to catch sea creature by 1%*(ce level)");
		$this->setApplicableTo(self::ITEM_FISHING_ROD);
		$this->setMaxLevel(4);
		$this->setEvents([PlayerPreFishEvent::class]);

		return new CustomEnchantIdentifier("angler", "Angler", true);
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerPreFishEvent) return;

		assert($player instanceof AetherPlayer);

		$player->getPveData()->setSeacreatureChance($player->getPveData()->getSeacreatureChance() + $enchantInstance->getLevel());

		Utils::executeLater(function() use($player, $enchantInstance) {
			$player->getPveData()->setSeacreatureChance($player->getPveData()->getSeacreatureChance() + $enchantInstance->getLevel());
		}, 40);
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerPreFishEvent) return false;

		return true;
	}
}