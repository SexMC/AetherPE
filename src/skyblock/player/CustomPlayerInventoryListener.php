<?php

declare(strict_types=1);

namespace skyblock\player;

use Exception;
use pocketmine\block\BlockFactory;
use pocketmine\block\Coal;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryListener;
use pocketmine\inventory\PlayerInventory;
use pocketmine\item\Book;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\Server;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\ICustomEnchant;
use skyblock\items\customenchants\BaseToggleableEnchant;
use skyblock\items\itemattribute\ItemAttributeHolder;
use skyblock\items\itemattribute\SkyBlockItemAttributes as ATTR;
use skyblock\items\PvEItemEditor;
use skyblock\items\SkyblockItem;
use skyblock\items\tools\SpecialWeapon;
use skyblock\items\tools\SpecialWeaponHandler;

class CustomPlayerInventoryListener implements InventoryListener {

	const ARMOR_APPLICABLES = [ICustomEnchant::ITEM_ARMOUR, ICustomEnchant::ITEM_HELMET, ICustomEnchant::ITEM_BOOTS, ICustomEnchant::ITEM_LEGGINGS, ICustomEnchant::ITEM_CHESTPLATE];

	public function __construct(private AetherPlayer $player){
		self::checkPvE($this->player, VanillaItems::AIR(), $this->player->getInventory()->getItemInHand());
	}

	public function onHeldItemIndexChange(int $oldIndex): void {
		$oldItem = $this->player->getInventory()->getItem($oldIndex);
		$newItem = $this->player->getInventory()->getItemInHand();

		$this->clearToggled($oldItem);
		if($newItem instanceof Book){
			return;
		}

		foreach(BaseToggleableEnchant::getToggleables($newItem) as $enchantInstance){
			/** @var \skyblock\items\customenchants\BaseToggleableEnchant $ce */
			$ce = $enchantInstance->getCustomEnchant();

			if(in_array($ce->getApplicableTo(), self::ARMOR_APPLICABLES)) continue;

			$ce->checkToggle($this->player, $newItem, $oldItem, $enchantInstance);
		}

		$old = $oldItem->getNamedTag()->getString(SpecialWeapon::TAG_SPECIAL_TOOL, "");
		$new = $newItem->getNamedTag()->getString(SpecialWeapon::TAG_SPECIAL_TOOL, "");

		if($old !== "" && $new === ""){
			SpecialWeaponHandler::getInstance()?->getWeapon($old)->onDeHold($this->player, $oldItem);
		} elseif($old === "" && $new !== ""){
			SpecialWeaponHandler::getInstance()?->getWeapon($new)->onHold($this->player, $newItem);
		}

		self::checkPvE($this->player, $oldItem, $newItem);
	}

	public function clearToggled(Item $oldItem) {
		/** @var CustomEnchantInstance $s */
		foreach(BaseToggleableEnchant::$array[$this->player->getName()] ?? [] as $s){
			if(($c = $s->getCustomEnchant()) instanceof BaseToggleableEnchant){
				$c->unToggle($this->player, $oldItem, $s);
				$c->removeToggled($this->player);
			}
		}

		BaseToggleableEnchant::$array[$this->player->getName()] = [];
	}


	public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem) : void{
		$newItem = $inventory->getItem($slot);

		if($slot !== $this->player->getInventory()->getHeldItemIndex()) return;

		/*if($newItem->getId() === ItemIds::FISHING_ROD && CustomFishingRod::getLevel($newItem) === -1){
			$inventory->setItem($slot, CustomFishingRod::getItem(1, 0));
			return;
		}*/

		if($oldItem->equals($newItem, false, true)){
			return;
		}

		$this->clearToggled($oldItem);
		if($newItem instanceof Book){
			return;
		}

		foreach(BaseToggleableEnchant::getToggleables($newItem) as $enchantInstance){
			/** @var BaseToggleableEnchant $ce */
			$ce = $enchantInstance->getCustomEnchant();

			if(in_array($ce->getApplicableTo(), self::ARMOR_APPLICABLES)) continue;

			$ce->checkToggle($this->player, $newItem, $newItem, $enchantInstance);
		}

		$old = $oldItem->getNamedTag()->getString(SpecialWeapon::TAG_SPECIAL_TOOL, "");
		$new = $newItem->getNamedTag()->getString(SpecialWeapon::TAG_SPECIAL_TOOL, "");

		if($old !== "" && $new === ""){
			SpecialWeaponHandler::getInstance()?->getWeapon($old)->onDeHold($this->player, $oldItem);
		} elseif($old === "" && $new !== ""){
			SpecialWeaponHandler::getInstance()?->getWeapon($new)->onHold($this->player, $newItem);
		}

		self::checkPvE($this->player, $oldItem, $newItem);
	}

	public static function checkPvE(AetherPlayer $player, Item $old, Item $new): void {
		$pve = $player->getPveData();


		try{
			if($old instanceof ItemAttributeHolder){
				$pve->setMaxIntelligence($pve->getMaxIntelligence() - $old->getItemAttribute(ATTR::INTELLIGENCE())->getValue());
				$pve->setMiningSpeed($pve->getMiningSpeed() - $old->getItemAttribute(ATTR::MINING_SPEED())->getValue());
				$pve->setStrength($pve->getStrength() - $old->getItemAttribute(ATTR::STRENGTH())->getValue());
				$pve->setMaxHealth($pve->getMaxHealth() - $old->getItemAttribute(ATTR::HEALTH())->getValue());
				$pve->setDefense($pve->getDefense() - $old->getItemAttribute(ATTR::DEFENSE())->getValue());
				$pve->setSpeed($pve->getSpeed() - $old->getItemAttribute(ATTR::SPEED())->getValue());
				$pve->setCritDamage($pve->getCritDamage() - $old->getItemAttribute(ATTR::CRITICAL_DAMAGE())->getValue());
				$pve->setCritChance($pve->getCritChance() - $old->getItemAttribute(ATTR::CRITICAL_CHANCE())->getValue());
				$pve->setFishingSpeed($pve->getFishingSpeed() - $old->getItemAttribute(ATTR::FISHING_SPEED())->getValue());
			}

			if($new instanceof ItemAttributeHolder){
				$pve->setMaxIntelligence($pve->getMaxIntelligence() + $new->getItemAttribute(ATTR::INTELLIGENCE())->getValue());
				$pve->setMiningSpeed($pve->getMiningSpeed() + $new->getItemAttribute(ATTR::MINING_SPEED())->getValue());
				$pve->setStrength($pve->getStrength() + $new->getItemAttribute(ATTR::STRENGTH())->getValue());
				$pve->setMaxHealth($pve->getMaxHealth() + $new->getItemAttribute(ATTR::HEALTH())->getValue());
				$pve->setDefense($pve->getDefense() + $new->getItemAttribute(ATTR::DEFENSE())->getValue());
				$pve->setSpeed($pve->getSpeed() + $new->getItemAttribute(ATTR::SPEED())->getValue());
				$pve->setCritDamage($pve->getCritDamage() + $new->getItemAttribute(ATTR::CRITICAL_DAMAGE())->getValue());
				$pve->setCritChance($pve->getCritChance() + $new->getItemAttribute(ATTR::CRITICAL_CHANCE())->getValue());
				$pve->setFishingSpeed($pve->getFishingSpeed() + $new->getItemAttribute(ATTR::FISHING_SPEED())->getValue());
			}
		} catch(Exception $e){
			Server::getInstance()->getLogger()->logException($e);
		}
	}

	public function onContentChange(Inventory $inventory, array $oldContents) : void{

		//NOOP
	}
}