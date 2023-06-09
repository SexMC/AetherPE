<?php

declare(strict_types=1);

namespace skyblock\items\misc\minion;

use pocketmine\item\ItemIdentifier;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;

class StorageChest extends SkyblockItem {

	const TAG_TYPE = "tag_storage_chest_type";

	const TYPE_SMALL = "small";
	const TYPE_MEDIUM = "medium";
	const TYPE_LARGE = "large";

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);


		$this->resetLore();
		$this->properties->setUnique(true);
	}

	public function getType(): string {
		return $this->getNamedTag()->getString(self::TAG_TYPE, self::TYPE_SMALL);
	}

	public function setType(string $type): self {
		$this->getNamedTag()->setString(self::TAG_TYPE, $type);
		match($type) {
			self::TYPE_SMALL => $this->getProperties()->setRarity(Rarity::uncommon()),
			self::TYPE_MEDIUM => $this->getProperties()->setRarity(Rarity::rare()),
			default => $this->getProperties()->setRarity(Rarity::epic()),
		};
		$this->resetLore();

		return $this;
	}

	public function resetLore(array $lore = []) : void{
		$type = $this->getType();


		$space = match($type) {
			self::TYPE_SMALL => 3,
			self::TYPE_MEDIUM => 9,
			self::TYPE_LARGE => 15,
		};

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . ucwords($type) . " Storage");
		$this->properties->setDescription([
			"§r§7Place this chest next to a",
			"§r§7minion and the minion will store",
			"§r§7items inside once its storage is",
			"§r§7full!",
			"§r",
			"§r§7Storage space: §a$space §7slots",
		]);

		parent::resetLore($lore);
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::uncommon());
	}
}