<?php

declare(strict_types=1);

namespace skyblock\items\misc\minion;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class Compactor extends SkyblockItem {

	private static array $blockForms = [
		ItemIds::WHEAT => [9, ItemIds::HAY_BALE],
		ItemIds::MELON => [9, ItemIds::MELON_BLOCK],
		ItemIds::SLIMEBALL => [9, ItemIds::SLIME_BLOCK],
		ItemIds::COAL => [9, ItemIds::COAL_BLOCK],
		ItemIds::IRON_INGOT => [9, ItemIds::IRON_BLOCK],
		ItemIds::GOLD_INGOT => [9, ItemIds::GOLD_BLOCK],
		ItemIds::DIAMOND => [9, ItemIds::DIAMOND_BLOCK],
		ItemIds::DYE => [9, ItemIds::LAPIS_BLOCK],
		ItemIds::REDSTONE => [9, ItemIds::REDSTONE_BLOCK],
		ItemIds::EMERALD => [9, ItemIds::EMERALD_BLOCK],
		ItemIds::CLAY => [4, ItemIds::CLAY_BLOCK],
		ItemIds::GLOWSTONE_DUST => [4, ItemIds::GLOWSTONE],
		ItemIds::SNOWBALL => [4, ItemIds::SNOW],
		ItemIds::ICE => [9, ItemIds::PACKED_ICE],

	];

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Compactor");
		$this->getProperties()->setDescription([
			"§r§7This item can be used as a",
			"§r§7minion upgrade. This will",
			"§r§7automatically turn materials",
			"§r§7that a minion produces into",
			"§r§7their block form.",
		]);

		$this->properties->setUnique(true);
		$this->resetLore();
	}

	/**
	 * @return array<int: countNeeded, int: ItemID>
	 */
	public static function getBlockItemByItem(Item $item): ?array {
		return self::$blockForms[$item->getId()] ?? null;
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::uncommon());
	}
}