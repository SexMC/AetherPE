<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;


class EmeraldBlade extends SkyBlockWeapon implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("emerald_sword", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));


		$this->properties->setDescription([
			"§r§7A powerful blade made from pure",
			"§r§2Emeralds§7. This blade becomes",
			"§r§7stronger as you carry more",
			"§r§6coins§7 in your purse",
		]);
		$this->properties->setRarity(Rarity::epic());

		$this->setCustomName("§r" . $this->getProperties()->getRarity()->getColor() . "Emerald Blade");


		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 130));

		$this->resetLore();
	}

	public function onAttackPve(AetherPlayer $player, PlayerAttackPveEvent $event) : void{
		parent::onAttackPve($player, $event);

		$purse = $player->getCurrentProfilePlayerSession()->getPurse();
		$add = max(1000, 2.5 * pow($purse, 1/4)); //2.5 nroot(2000000000,4) (2.5 * vierde machtswortel(x))

		$event->increaseDamage((int) $add, "emerald blade");
	}

}