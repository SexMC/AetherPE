<?php

declare(strict_types=1);

namespace skyblock\player;

use Exception;
use pocketmine\inventory\ArmorInventory;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryListener;
use pocketmine\item\Armor;
use pocketmine\item\Item;
use pocketmine\Server;
use skyblock\items\armor\ArmorSet;
use skyblock\items\customenchants\CustomEnchantInstance;
use skyblock\items\customenchants\BaseToggleableEnchant;
use skyblock\items\itemattribute\ItemAttributeHolder;
use skyblock\items\itemattribute\SkyBlockItemAttributes as ATTR;
use skyblock\items\ItemEditor;
use skyblock\items\masks\IMaskHolder;
use skyblock\items\masks\MasksHandler;
use skyblock\items\PvEItemEditor;
use skyblock\items\sets\SpecialSetHandler;
use skyblock\utils\CustomEnchantUtils;

//TODO: same for held item so normal inventory
class CustomPlayerArmorInventoryListener implements InventoryListener {

	public function __construct(private AetherPlayer $player){ }

	public function onSlotChange(Inventory $inventory, int $slot, Item $oldItem) : void{
		$newItem = $inventory->getItem($slot);

		if($oldItem->equals($newItem)) return;

		ArmorSet::check($this->player);
		//SpecialSetHandler::getInstance()->check($this->player);


		foreach(BaseToggleableEnchant::getToggleables($oldItem) as $enchantInstance){
			/** @var \skyblock\items\customenchants\BaseToggleableEnchant $ce */
			$ce = $enchantInstance->getCustomEnchant();
			$ce->checkToggle($this->player, $newItem, $oldItem, $enchantInstance);
		}

		foreach(BaseToggleableEnchant::getToggleables($newItem) as $enchantInstance){
			/** @var \skyblock\items\customenchants\BaseToggleableEnchant $ce */
			$ce = $enchantInstance->getCustomEnchant();
			$ce->checkToggle($this->player, $newItem, $oldItem, $enchantInstance);
		}
		$this->checkPvE($oldItem, $newItem);

		$this->checkMask($slot, $oldItem, $newItem);
		$this->player->setMaxHealth(CustomEnchantUtils::getMaxHealth($this->player));
	}

	public function checkPvE(Item $old, Item $new): void {
		$pve = $this->player->getPveData();

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
	public function checkMask(int $slot, Item $oldItem, Item $newItem): void {
		if($oldItem instanceof IMaskHolder){
			if(($mask = $oldItem->getMask())){
				$mask->onInternalTakeOff($this->player, $oldItem, $newItem);
				return;
			}
		}

		if($newItem instanceof IMaskHolder){
			if(($mask = $newItem->getMask())){
				$mask->onInternalWear($this->player, $oldItem, $newItem);
				return;
			}
		}

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

	/**
	 * @param Inventory $inventory
	 * @param Item[]     $oldContents
	 */
	public function onContentChange(Inventory $inventory, array $oldContents) : void{
		//SpecialSetHandler::getInstance()->check($this->player);
		ArmorSet::check($this->player);

		foreach($oldContents as $k => $oldItem){
			if($oldItem->isNull()) continue;


			$newItem = $inventory->getItem($k);
			foreach(BaseToggleableEnchant::getToggleables($oldItem) as $enchantInstance){
				/** @var \skyblock\items\customenchants\BaseToggleableEnchant $ce */
				$ce = $enchantInstance->getCustomEnchant();
				$ce->checkToggle($this->player, $newItem, $oldItem, $enchantInstance);
			}

			foreach(BaseToggleableEnchant::getToggleables($newItem) as $enchantInstance){
				/** @var \skyblock\items\customenchants\BaseToggleableEnchant $ce */
				$ce = $enchantInstance->getCustomEnchant();
				$ce->checkToggle($this->player, $newItem, $oldItem, $enchantInstance);
			}

			$this->checkPvE($oldItem, $newItem);

			$this->checkMask($k, $oldItem, $newItem);
		}
	}
}