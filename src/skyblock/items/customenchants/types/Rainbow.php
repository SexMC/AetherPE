<?php

declare(strict_types=1);

namespace skyblock\items\customenchants\types;

use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Event;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerKillPveEvent;
use skyblock\items\customenchants\CustomEnchantIdentifier;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseReactiveEnchant;
use skyblock\items\rarity\Rarity;
use skyblock\utils\Utils;

class Rainbow extends BaseReactiveEnchant {
	public function prepare() : CustomEnchantIdentifier{
		$this->setRarity(Rarity::rare());
		$this->setDescription("Chance to give a random coloured wool when slaughtering sheeps");
		$this->setApplicableTo(self::ITEM_WEAPONS);
		$this->setMaxLevel(4);
		$this->setEvents([PlayerKillPveEvent::class]);

		return new CustomEnchantIdentifier("rainbow", "Rainbow");
	}


	public function Reaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : void{
		if(!$event instanceof PlayerKillPveEvent) return;

		$item = VanillaBlocks::WOOL()->setColor(DyeColor::getAll()[array_rand(DyeColor::getAll())])->asItem();
		Utils::addItem($player, $item);
	}

	public function preReaction(Player $player, Event $event, CustomEnchantInstance $enchantInstance) : bool{
		if(!$event instanceof PlayerKillPveEvent) return false;

		if($event->getEntity()->getNetworkID() === EntityIds::SHEEP){
			return mt_rand(1, 100) <= $enchantInstance->getLevel() * 15;
		}

		return false;
	}
}