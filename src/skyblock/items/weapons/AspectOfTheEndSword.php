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
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ability\AreaDamagePercentageAbility;
use skyblock\items\ability\TeleportAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;
use skyblock\utils\PveUtils;


class AspectOfTheEndSword extends SkyBlockWeapon implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("aspect_of_the_end_sword", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));


		$this->properties->setDescription([
			"§r§7Swing your sword to",
			"§r§7obliterate nearby enemies",
			"§r",
			"§r§6§lABILITY \"§r§eInstant Transmission§l§6\"",
			"§r§e\"Teleport §a8§e blocks ahead of",
			"§r§eyou and gain §a+50 " . PveUtils::getSpeed(),
			"§r§efor §a3 seconds.\"",
			"§r§b§lMANA COST \"§r§350§l§b\"",
		]);


		$this->properties->setRarity(Rarity::rare());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 100));
		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), 100));
	}



	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		(new TeleportAbility(8, "Instant Transmission", 1, 0))->start($player, $this);

		return parent::onClickAir($player, $directionVector);
	}
}