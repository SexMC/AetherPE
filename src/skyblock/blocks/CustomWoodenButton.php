<?php

declare(strict_types=1);

namespace skyblock\blocks;

use pocketmine\block\Button;
use pocketmine\block\WoodenButton;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\RedstonePowerOnSound;
use skyblock\events\block\ButtonClickEvent;

class CustomWoodenButton extends WoodenButton {

	protected function getActivationTime() : int{
		return 30;
	}

	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		if(!$this->pressed){
			$this->pressed = true;
			$world = $this->position->getWorld();
			$world->setBlock($this->position, $this);
			$world->scheduleDelayedBlockUpdate($this->position, $this->getActivationTime());
			$world->addSound($this->position->add(0.5, 0.5, 0.5), new RedstonePowerOnSound());

			if($player){
				(new ButtonClickEvent($this, $player))->call();
			}
		}

		return true;
	}


	public function hasEntityCollision() : bool{
		return false; //TODO: arrows activate wooden buttons
	}
}