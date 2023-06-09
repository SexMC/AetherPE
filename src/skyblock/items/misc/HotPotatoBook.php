<?php

declare(strict_types=1);

namespace skyblock\items\misc;

use customiesdevs\customies\item\CreativeInventoryInfo;
use customiesdevs\customies\item\ItemComponents;
use customiesdevs\customies\item\ItemComponentsTrait;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\player\Player;
use skyblock\items\itemattribute\ItemAttributeInstance;
use skyblock\items\itemattribute\SkyBlockItemAttributes;
use skyblock\items\rarity\Rarity;
use skyblock\items\SkyblockArmor;
use skyblock\items\SkyblockItem;
use skyblock\items\SkyblockItemProperties;
use skyblock\items\SkyblockItems;
use skyblock\items\SkyBlockWeapon;
use skyblock\Main;
use skyblock\utils\PveUtils;

class HotPotatoBook extends SkyblockItem implements ItemComponents{
	use ItemComponentsTrait;

	const EXTRA_ATTRIBUTE_TAG = "hot_potato_book";

	public function __construct(ItemIdentifier $identifier, string $name = "Unknown"){
		parent::__construct($identifier, $name);

		$this->initComponent("hot_potato_book", new CreativeInventoryInfo(CreativeInventoryInfo::CATEGORY_EQUIPMENT));

		$this->setCustomName("§r" . $this->properties->getRarity()->getColor() . "Hot Potato Book §7(Drag n' drop)");
		$this->getProperties()->setDescription([
			"§r",
			"§r§7When applied to armor, grants",
			"§r§a+2" . PveUtils::getDefense() . "§7 and §c+4" . PveUtils::getHealth(),
			"§r",
			"§r§7When applied to weapons, grants",
			"§r§c+2" . PveUtils::getStrength() . "§7 and §c+2" . PveUtils::getDamage(),
			"§r",
			"§r§7This can be applied to an item",
			"§r§7upto §a10§7 times.",
		]);

		$this->resetLore();
	}

	public function onTransaction(Player $player, Item $itemClicked, Item $itemClickedWith, SlotChangeAction $itemClickedAction, SlotChangeAction $itemClickedWithAction, InventoryTransactionEvent $event) : void{
		if(!$itemClickedWith instanceof HotPotatoBook) return;

		$added = false;

		if($itemClicked instanceof SkyblockArmor){
			$attr = $itemClicked->getExtraAttributeByName(self::EXTRA_ATTRIBUTE_TAG, SkyBlockItemAttributes::DEFENSE());

			$appliedCount = $attr->getValue() === 0 ? 0 : $attr->getValue() / 2;

			if($appliedCount >= 10){
				$player->sendMessage(Main::PREFIX . "You cannot apply more than 10 hot potato books to an item");
				return;
			}

			$appliedCount++;

			$itemClicked->addExtraAtribute(self::EXTRA_ATTRIBUTE_TAG, new ItemAttributeInstance(SkyBlockItemAttributes::DEFENSE(), $appliedCount * 2));
			$itemClicked->addExtraAtribute(self::EXTRA_ATTRIBUTE_TAG, new ItemAttributeInstance(SkyBlockItemAttributes::HEALTH(), $appliedCount * 4));
			$added = true;
		}

		if($itemClicked instanceof SkyBlockWeapon){
			$attr = $itemClicked->getExtraAttributeByName(self::EXTRA_ATTRIBUTE_TAG, SkyBlockItemAttributes::STRENGTH());

			$appliedCount = $attr->getValue() === 0 ? 0 : $attr->getValue() / 2;

			if($appliedCount >= 10){
				$player->sendMessage(Main::PREFIX . "You cannot apply more than 10 hot potato books to an item");
				return;
			}

			$appliedCount++;

			$itemClicked->addExtraAtribute(self::EXTRA_ATTRIBUTE_TAG, new ItemAttributeInstance(SkyBlockItemAttributes::STRENGTH(), $appliedCount * 2));
			$itemClicked->addExtraAtribute(self::EXTRA_ATTRIBUTE_TAG, new ItemAttributeInstance(SkyBlockItemAttributes::DAMAGE(), $appliedCount * 4));
			$added = true;
		}

		if($added === true){
			$itemClicked->resetLore();

			$itemClickedWith->pop();
			$event->cancel();
			$itemClickedWithAction->getInventory()->setItem($itemClickedWithAction->getSlot(), $itemClickedWith);
			$itemClickedAction->getInventory()->setItem($itemClickedAction->getSlot(), $itemClicked);
			$player->sendMessage(Main::PREFIX . "Successfully added a hot potato book to " . $itemClicked->getName());
		} else {
			$player->sendMessage(Main::PREFIX . "You can only apply hot potato books to armor and weapons");
		}
	}

	public function buildProperties() : SkyblockItemProperties{
		return (new SkyblockItemProperties())->setRarity(Rarity::epic());
	}
}