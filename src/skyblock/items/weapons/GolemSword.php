<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use customiesdevs\customies\item\component\HandEquippedComponent;
use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\block\Block;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ability\AreaDamageAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;


class GolemSword extends SkyBlockWeapon implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->initComponent("golem_sword", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));


		$this->properties->setDescription([
			"§r§7I'm the cause of all deaths,",
			"§r§7i created it.",
			"§r",
			"§r§4§lABILITY \"§r§3Iron Punch§l§4\"",
			"§r§c\"Punch the ground, damaging",
			"§r§c\"enemies in a hexagon around you",
			"§r§cfor §a255§c base magic damage\"",
			"§r§b§lMANA COST \"§r§370§l§b\"",
		]);

		$this->properties->setRarity(Rarity::rare());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 80));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 125));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), 25));
	}

	public function onAttackPve(AetherPlayer $player, PlayerAttackPveEvent $event) : void{
		parent::onAttackPve($player, $event);

		if($event->getCause() === null){
			(new AreaDamageAbility(8, 255, true, "§3Iron Punch", 70, 0))->start($player, $this);
		}
	}

	public function onInteractBlock(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : ItemUseResult{
		(new AreaDamageAbility(8, 255, true, "§3Iron Punch", 70, 0))->start($player, $this);

		return parent::onInteractBlock($player, $blockReplace, $blockClicked, $face, $clickVector);
	}
}