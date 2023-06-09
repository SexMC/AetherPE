<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use customiesdevs\customies\item\component\ArmorComponent;
use customiesdevs\customies\item\component\IconComponent;
use customiesdevs\customies\item\component\ItemComponent;
use customiesdevs\customies\item\component\WearableComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ability\AreaDamagePercentageAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;


class CleaverSword extends SkyBlockWeapon implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("cleaver_sword", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));


		$this->properties->setDescription([
			"§r§7Swing your sword to",
			"§r§7obliterate nearby enemies",
			"§r",
			"§r§6§lABILITY \"§r§eCleave§l§6\"",
			"§r§e\"When hitting an entity, monsters",
			"§r§ein a §a3§e block range will be hit for",
			"§r§ea portion of that damage.\"",
		]);


		$this->properties->setRarity(Rarity::uncommon());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 40));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 10));
	}
	
	public function onAttackPve(AetherPlayer $player, PlayerAttackPveEvent $event) : void{
		parent::onAttackPve($player, $event);

		if($event->getCause() === null){
			(new AreaDamagePercentageAbility(3, 1.45, $event, "§eCleave", 0, 0))->start($player, $this);
		}
	}
}