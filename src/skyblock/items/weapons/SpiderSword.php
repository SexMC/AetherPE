<?php

declare(strict_types=1);

namespace skyblock\items\weapons;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\item\ItemIdentifier;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\items\ability\IncreasedDamageAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyBlockWeapon;
use skyblock\player\AetherPlayer;

class SpiderSword extends SkyBlockWeapon implements ItemComponents{
	use ItemComponentsTrait;

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("spider_sword", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT, CreativeInventoryInfo::GROUP_SWORD));


		$this->properties->setDescription([
			"§r§7Deals §a+100%§7 damage to",
			"§r§7spiders, cave spiders",
			"§r§7and silverfish"
		]);

		$this->properties->setRarity(Rarity::common());


		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 30));
	}

	public function onAttackPve(AetherPlayer $player, PlayerAttackPveEvent $event) : void{
		parent::onAttackPve($player, $event);

		$a = new IncreasedDamageAbility([EntityIds::SPIDER, EntityIds::CAVE_SPIDER, EntityIds::SILVERFISH], 2, $event, "Spider Sword", 0, 0);
		$a->start($player, $this);
	}
}