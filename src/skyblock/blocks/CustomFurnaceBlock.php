<?php

declare(strict_types=1);

namespace skyblock\blocks;



use pocketmine\block\Furnace;
use pocketmine\crafting\FurnaceType;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use skyblock\tiles\HyperFurnaceTile;

class CustomFurnaceBlock extends Furnace {
	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if($player instanceof Player){
			$furnace = $this->position->getWorld()->getTile($this->position);
			if(($furnace instanceof HyperFurnaceTile) && $furnace->canOpenWith($item->getCustomName())){
				$player->setCurrentWindow($furnace->getInventory());
			}
		}

		return true;
	}

	public function onScheduledUpdate() : void{
		$world = $this->position->getWorld();
		$furnace = $world->getTile($this->position);
		if(($furnace instanceof HyperFurnaceTile) && $furnace->onUpdate()){
			if(mt_rand(1, 60) === 1){ //in vanilla this is between 1 and 5 seconds; try to average about 3
				$world->addSound($this->position, FurnaceType::FURNACE()->getCookSound());
			}
			$world->scheduleDelayedBlockUpdate($this->position, 1); //TODO: check this
		}
	}

}