<?php

declare(strict_types=1);

namespace skyblock\items\accessory;

use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\Main;
use skyblock\player\AetherPlayer;

abstract class AccessoryItem extends SkyblockItem {

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : ItemUseResult{
		assert($player instanceof AetherPlayer);

		if(count($player->getAccessoryData()->getAccessories()) >= 3 + $player->getAccessoryData()->getExtraAccessorySlots()){
			$player->sendMessage(Main::PREFIX . "Your accessory storage is full!");
			return ItemUseResult::FAIL();
		}

		$all = $player->getAccessoryData()->getAccessories();
		if(isset($all[$this->getAccessoryName()])){
			$player->sendMessage(Main::PREFIX . "You already have a §c{$this->getAccessoryName()}§7 in your accessory collection.");
			return ItemUseResult::FAIL();
		}

		$cl = clone $this;
		$cl->setCount(1);
		$all[$this->getAccessoryName()] = $cl;
		$player->getAccessoryData()->setAccessories($all);
		$player->sendMessage(Main::PREFIX . "§c{$this->getAccessoryName()} §7has been added to your accessory collection");

		$this->pop();
		$player->getInventory()->setItemInHand($this);

		return ItemUseResult::FAIL();
	}

	public abstract function onActivate(AetherPlayer $player, AccessoryItem $item) : void;

	public abstract function onDeactivate(AetherPlayer $player, AccessoryItem $item) : void;

	public abstract function getAccessoryName(): string;

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setType(SkyblockItemProperties::ITEM_TYPE_ACCESSORY);
	}
}