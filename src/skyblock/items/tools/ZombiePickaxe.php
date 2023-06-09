<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\block\BlockLegacyIds;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\ItemIdentifier;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherEffect;
use skyblock\player\AetherPlayer;

class ZombiePickaxe extends SkyBlockPickaxe {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->properties->setDescription([
			"§r§7Grants Haste II for §a5s",
			"§r§7when breaking ores!"
		]);

		$this->properties->setRarity(Rarity::common());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::MINING_SPEED(), 190));
	}

	public function onCustomDestroyBlock(AetherPlayer $player, BlockBreakEvent $event) : void{
		parent::onCustomDestroyBlock($player, $event);


		$b = $event->getBlock();

		$arr = [
			BlockLegacyIds::GOLD_ORE,
			BlockLegacyIds::REDSTONE_ORE,
			BlockLegacyIds::IRON_ORE,
			BlockLegacyIds::DIAMOND_ORE,
			BlockLegacyIds::EMERALD_ORE,
			BlockLegacyIds::LAPIS_ORE,
			BlockLegacyIds::COAL_ORE,
			BlockLegacyIds::QUARTZ_ORE,
		];

		if(in_array($b->getId(), $arr)){
			$player->getEffects()->add(new AetherEffect(VanillaEffects::HASTE(), 5 * 20, 1));
		}
	}
}