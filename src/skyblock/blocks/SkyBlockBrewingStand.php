<?php

declare(strict_types=1);

namespace skyblock\blocks;

use pocketmine\block\BlockBreakInfo as BreakInfo;
use pocketmine\block\BlockIdentifier as BID;
use pocketmine\block\BlockLegacyIds as Ids;
use pocketmine\block\BlockToolType as ToolType;
use pocketmine\block\BrewingStand;
use pocketmine\block\tile\BrewingStand as TileBrewingStand;
use pocketmine\block\utils\BrewingStandSlot;
use pocketmine\item\Item;
use pocketmine\item\ItemIds;
use pocketmine\item\ToolTier;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\menus\recipe\BrewingMenu;
use skyblock\tiles\BrewingStandTile;

class SkyBlockBrewingStand extends BrewingStand {

	public function __construct(){
		parent::__construct(new BID(Ids::BREWING_STAND_BLOCK, 0, ItemIds::BREWING_STAND, BrewingStandTile::class), "Brewing Stand", new BreakInfo(0.5, ToolType::PICKAXE, ToolTier::WOOD()->getHarvestLevel()));
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($player instanceof Player){
			$stand = $this->position->getWorld()->getTile($this->position);
			if($stand instanceof BrewingStandTile && $stand->canOpenWith($item->getCustomName())){
				(new BrewingMenu($stand))->send($player);
			}
		}

		return true;
	}

	public function onScheduledUpdate() : void{
		$world = $this->position->getWorld();
		$brewing = $world->getTile($this->position);
		if($brewing instanceof BrewingStandTile){
			//if($brewing->onUpdate()){
			//	$world->scheduleDelayedBlockUpdate($this->position, 1);
			//}

			$changed = false;
			foreach(BrewingStandSlot::getAll() as $slot){
				$occupied = !$brewing->getInventory()->isSlotEmpty($slot->getSlotNumber());
				if($occupied !== $this->hasSlot($slot)){
					$this->setSlot($slot, $occupied);
					$changed = true;
				}
			}

			if($changed){
				$world->setBlock($this->position, $this);
			}
		}
	}
}