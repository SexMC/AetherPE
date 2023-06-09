<?php

declare(strict_types=1);

namespace skyblock\items\misc;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\menus\items\PersonalCompactorMenu;
use skyblock\traits\PlayerCooldownTrait;

class PersonalCompactor4000 extends SkyblockItem {
	use PlayerCooldownTrait;

	const TAG_SELECTED_ITEM = "tag_compactor_selected_item";



	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->setCustomName(TextFormat::RESET . $this->properties->getRarity()->getColor() . "Personal Compactor 4000");

		$this->resetLore();
		$this->makeGlow();
		$this->properties->setUnique(true);
	}

	public function resetLore(array $parentLore = []) : void{
		$selected = $this->getSelectedItem();

		$lore = [
			"§r§7Automatically turns certain",
			"§r§7materials in your inventory into",
			"§r§7their enchanted form",
			"§r",
		];


		if($selected === null){
			$lore[] = "§r§eRight-Click to configure!";
		} else {
			$lore[] = "§r§7Selected item: " . $selected->getCustomName();
		}

		$lore[] = "§r";
		$lore[] = "§r§8§oNeeds to be in your hotbar to work.";

		$this->properties->setDescription($lore);

		parent::resetLore($parentLore);
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		if(!$this->isOnCooldown($player)){
			$this->setCooldown($player, 1);
			(new PersonalCompactorMenu($this))->send($player);
		}

		return parent::onClickAir($player, $directionVector);
	}

	public function getSelectedItem(): ?Item {
		$s = $this->getNamedTag()->getString(self::TAG_SELECTED_ITEM, "");

		if($s === "") return null;


		return Item::jsonDeserialize(json_decode($s, true));
	}

	public function setSelectedItem(?Item $selected): self {
		if($selected === null){
			$this->getNamedTag()->removeTag(self::TAG_SELECTED_ITEM);
			$this->resetLore();
			return $this;
		}


		$this->getNamedTag()->setString(self::TAG_SELECTED_ITEM, json_encode($selected->jsonSerialize()));
		$this->resetLore();

		return $this;
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::uncommon());
	}
}