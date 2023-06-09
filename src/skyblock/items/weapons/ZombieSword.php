<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\items\ability\HealAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;

class ZombieSword extends SkyBlockWeapon implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
		$this->initComponent("zombie_sword", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));


		$this->properties->setDescription([
			"§r§7Zombies eat brains, you are safe.",
			"§r",
			"§r§6§lABILITY \"§r§eInstant Heal§l§6\"",
			"§r§e\"Heal for §c120 §e+ §c5%" . PveUtils::getHealthSymbol() . "§e and",
			"§r§eheal players within §a7 §eblocks",
			"§r§efor §c40" . PveUtils::getHealthSymbol(),
			"§r§b§lMANA COST \"§r§370§l§b\"",
		]);

		$this->properties->setRarity(Rarity::rare());


		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 100));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 50));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::INTELLIGENCE(), 50));
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		parent::onClickAir($player, $directionVector);

		assert($player instanceof AetherPlayer);

		(new HealAbility(120 + $player->getPveData()->getMaxHealth() * 0.05, 7, "§eInstant Heal", 70, 2))->start($player, $this);

		return ItemUseResult::SUCCESS();
	}
}