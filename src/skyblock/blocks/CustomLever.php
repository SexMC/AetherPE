<?php

declare(strict_types=1);

namespace skyblock\blocks;

use pocketmine\block\Button;
use pocketmine\block\Lever;
use pocketmine\block\WoodenButton;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\sound\RedstonePowerOffSound;
use pocketmine\world\sound\RedstonePowerOnSound;
use skyblock\events\block\ButtonClickEvent;
use skyblock\events\block\LeverPullEvent;

class CustomLever extends Lever {


	public function onInteract(Item $item, int $face, Vector3 $clickVector, ?Player $player = null) : bool{
		$this->activated = !$this->activated;
		$world = $this->position->getWorld();
		$world->setBlock($this->position, $this);
		$world->addSound(
			$this->position->add(0.5, 0.5, 0.5),
			$this->activated ? new RedstonePowerOnSound() : new RedstonePowerOffSound()
		);

		if($player){
			(new LeverPullEvent($this, $player))->call();
		}

		return true;
	}
}