<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\Shovel;
use skyblock\items\ability\DestroyConnectedBlocksAbility;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockTool;
use skyblock\player\AetherPlayer;

class JungleAxe extends SkyBlockAxe {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->properties->setDescription([
			"§r§7A powerful wooden axe which can break",
			"§r§7upto 10 connected logs at once.",
			"§r§8Cooldown: §a2s",
		]);

		$this->properties->setRarity(Rarity::uncommon());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 10));
	}

	public function onCustomDestroyBlock(AetherPlayer $player, BlockBreakEvent $event) : void{
		parent::onCustomDestroyBlock($player, $event);


		(new DestroyConnectedBlocksAbility($event->getBlock(), 10, "Jungle Axe Ability", 0, 2))->start($player, $this);
	}
}