<?php

declare(strict_types=1);

namespace skyblock\items\misc\storagesack;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\menus\items\StorageSackMenu;
use skyblock\traits\PlayerCooldownTrait;

abstract class StorageSack extends SkyblockItem {
	use PlayerCooldownTrait;

	const SIZE_SMALL = 640;
	const SIZE_MEDIUM = 2240;
	const SIZE_LARGE = 20160;

	const TAG_CAPACITY = "tag_storage_sack_capacity";

	/** @var Item[]  */
	private array $storableItems;


	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{

		if(!$this->isOnCooldown($player)){
			$this->setCooldown($player, 1);

			(new StorageSackMenu($this))->send($player);
		}

		return parent::onClickAir($player, $directionVector);
	}

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		$this->storableItems = $this->buildStorageList();


		parent::__construct($identifier, $name);

		//var_dump("construct called");

		if($this->getNamedTag()->getCompoundTag("storage_items") === null){
			$compoundTag = new CompoundTag();
			foreach($this->storableItems as $v){
				$compoundTag->setInt($v->getId() . ":" . $v->getMeta(), 0);
			}
			$this->getNamedTag()->setTag("storage_items", $compoundTag);
		}





		$this->setCapacity($this->getCapacity());
		$this->resetLore();
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setUnique(true);
	}

	public function getCapacity(): int {
		return $this->getNamedTag()->getInt(self::TAG_CAPACITY, self::SIZE_SMALL);
	}

	public function setCapacity(int $capacity): self {
		$this->getNamedTag()->setInt(self::TAG_CAPACITY, $capacity);

		match($capacity){
			self::SIZE_SMALL => $this->getProperties()->setRarity(Rarity::uncommon()),
			self::SIZE_MEDIUM => $this->getProperties()->setRarity(Rarity::rare()),
			self::SIZE_LARGE => $this->getProperties()->setRarity(Rarity::epic()),
			default => $this->getProperties()->setRarity(Rarity::epic())
		};

		$this->setCustomName("§r" . $this->getPrefixBySize($capacity) . " {$this->getTypeName()} Sack");
		$this->resetLore();

		return $this;
	}

	public function getPrefixBySize(int $size): string {
		return match ($size) {
			default => "§aSmall",
			self::SIZE_MEDIUM => "§3Medium",
			self::SIZE_LARGE => "§5Large",
		};
	}

	public function resetLore(array $parentLore = []) : void{
		$lore = [
			"§r§7Item pickups go directly into",
			"§r§7your sacks.",
			"§r",
		];


		$string = wordwrap(implode("§r§7, §a", array_map(fn(Item $i) => $i->getName(), $this->storableItems)), 60);
		$lore[] = "§r§7Items: §a" . $string;


		$lore[] = "";
		$lore[] = "§r§7Capacity: §e" . number_format($this->getCapacity()) . " per item";
		$lore[] = "§r§8Sacks sum their capacity.";
		$lore[] = "§r§8Item must be in your hotbar.";

		$this->properties->setDescription($lore);

		parent::resetLore($parentLore);
	}

	/**
	 * @return Item[]
	 */
	public abstract function buildStorageList(): array; //array of items it will be able to store
	public abstract function getTypeName(): string;
}