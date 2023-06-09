<?php

declare(strict_types=1);

namespace skyblock\items\tools;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\VanillaItems;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\player\AetherPlayer;

class FlintShovel extends SkyBlockShovel {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->properties->setDescription([
			"§r§7Grants a §a100%§7 chance of",
			"§r§7receiving flint from gravel.",
			"§r",
			"§r§7This item can be also used as a",
			"§r§7minion upgrade to allow gravel",
			"§r§7minions to always receive flint.",
		]);

		$this->properties->setRarity(Rarity::common());

		$this->setItemAttribute(new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), 10));
	}


	public function onCustomDestroyBlock(AetherPlayer $player, BlockBreakEvent $event) : void{
		parent::onCustomDestroyBlock($player, $event);


		$event->setDrops([VanillaItems::FLINT()]);
	}
}