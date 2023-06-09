<?php

declare(strict_types=1);

namespace skyblock\misc\areas;

use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\player\AetherPlayer;

class Area {

	private AxisAlignedBB $bb;

	public function __construct(private string $name, int $minX, int $minY, int $minZ, int $maxX, int $maxY, int $maxZ){
		$this->bb = new AxisAlignedBB(
			min($minX, $maxX),
			min($minY, $maxY),
			min($minZ, $maxZ),

			max($minX, $maxX),
			max($minY, $maxY),
			max($minZ, $maxZ),
		);
	}

	/**
	 * @return AxisAlignedBB
	 */
	public function getBoundingBox() : AxisAlignedBB{
		return $this->bb;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}


	/**
	 * @return AetherPlayer[]
	 */
	public function getPlayersInside(): array {
		$r = [];
		foreach(Server::getInstance()->getWorldManager()->getDefaultWorld()->getNearbyEntities($this->getBoundingBox()) as $e){
			if($e instanceof AetherPlayer){
				$r[$e->getName()] = $e;
			}
		}

		return $r;
	}

	public function message(string|array $message){
		foreach($this->getPlayersInside() as $p) {
			$p->sendMessage($message);
		}
	}

}