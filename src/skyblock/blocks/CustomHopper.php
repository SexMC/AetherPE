<?php

declare(strict_types=1);

namespace skyblock\blocks;

use dktapps\pmforms\MenuOption;
use pocketmine\block\Hopper;
use pocketmine\block\inventory\HopperInventory;
use pocketmine\block\tile\Hopper as HopperTile;
use pocketmine\block\tile\Tile;
use pocketmine\entity\object\ItemEntity;
use pocketmine\inventory\Inventory;
use pocketmine\inventory\InventoryHolder;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Facing;
use skyblock\caches\block\TickableBlockSchedulerCache;

class CustomHopper extends Hopper implements ITickableBlock{

	private bool $alreadyScheduled = false;


	public function getInventory() : ?HopperInventory{
		$tile = $this->position->getWorld()->getTileAt($this->position->x, $this->position->y, $this->position->z);
		return $tile instanceof HopperTile ? $tile->getInventory() : null;
	}

	public function onScheduledUpdate() : void{
		if(!$this->alreadyScheduled){
			$this->alreadyScheduled = true;
			TickableBlockSchedulerCache::getInstance()->schedule($this, mt_rand(10, 15));
		}
	}

	public function onUpdate() : void{
		$tile = $this->getPosition()->getWorld()->getTile($this->getPosition());
		if($tile instanceof HopperTile){
			$inventory = $this->getInventory();


			if(mt_rand(1, 3) === 1){
				$bb = AxisAlignedBB::one()->offset($this->position->x, $this->position->y + 1, $this->position->z);
				$bb->minY = $this->position->y - 0.50;

				foreach($this->getPosition()->getWorld()->getChunkEntities($this->getPosition()->getFloorX() >> 4, $this->getPosition()->getFloorZ() >> 4) as $entity){
					if($entity->isFlaggedForDespawn()) continue;
					if(!$entity instanceof ItemEntity) continue;

					if(!$bb->intersectsWith($entity->getBoundingBox())) continue;

					$item = $entity->getItem();
					if($inventory->canAddItem($item)){
						$inventory->addItem($item);
						$entity->flagForDespawn();
					}
				}
			}

			$aboveBlock = $this->position->getWorld()->getBlock($this->position->add(0, 1, 0));
			$container = $this->position->getWorld()->getTile($aboveBlock->getPosition());
			$pulledItem = false;
			if($container instanceof InventoryHolder){
				$inventory = $container->getInventory();
				$item = $this->getFirstItem($inventory);
				if($item !== null){
					$pulledItem = $this->pullItem($item, $inventory);
				}
			}


			$pos = $this->position->asVector3()->getSide($this->getFacing());
			$tile = $this->position->world->getTileAt($pos->x, $pos->y, $pos->z);
			if($tile instanceof InventoryHolder && !$pulledItem){
				$item = $this->getFirstItem($this->getInventory());
				if($item !== null){
					$this->transferItem($item, $tile);
				}
			}

			TickableBlockSchedulerCache::getInstance()->schedule($this, mt_rand(10, 15));
		}
	}

	public function getFirstItem(Inventory $inventory) : ?Item{
		foreach($inventory->getContents() as $slot){
			if($slot !== null and !$slot->isNull()){
				return $slot;
			}
		}
		return null;
	}

	public function pullItem(Item $trItem, Inventory $inventory) : bool{
		$item = clone $trItem;
		$inv = $this->getInventory();
		if($inv->canAddItem($item)){
			$inv->addItem($item);
			$inventory->removeItem($item);
			return true;
		}
		return false;
	}

	public function transferItem(Item $trItem, InventoryHolder $inventoryHolder) : bool{
		$item = clone $trItem;
		$inv = $inventoryHolder->getInventory();
		if($inv->canAddItem($item)){
			$inv->addItem($item);
			$this->getInventory()->removeItem($item);
			return true;
		}
		return false;
	}
}