<?php

declare(strict_types=1);

namespace skyblock\entity\object;

use pocketmine\block\VanillaBlocks;
use pocketmine\entity\object\FallingBlock;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\sound\AnvilFallSound;

class CarpenterFallingBlock extends FallingBlock {

	private ?Player $player = null;

	protected function initEntity(CompoundTag $nbt) : void{
		parent::initEntity($nbt);
		$this->setCanSaveWithChunk(false);
	}

	public function flagForDespawn() : void{
		parent::flagForDespawn();

		if($this->player !== null && $this->player->isOnline()){
			$p = $this->player;

			$p->setHealth($p->getHealth() - 6);

			$pk = ActorEventPacket::create($p->getId(), ActorEvent::HURT_ANIMATION, 0);
			$p->getWorld()->broadcastPacketToViewers($p->getLocation(), $pk);
			$p->getWorld()->addSound($p->getLocation(), new AnvilFallSound());

			$p->getWorld()->addParticle($p->getLocation(), new BlockBreakParticle(VanillaBlocks::STONE()));

		}
	}

	/**
	 * @param Player $player
	 */
	public function setPlayer(Player $player) : void{
		$this->player = $player;
	}

	/**
	 * @return Player
	 */
	public function getPlayer() : Player{
		return $this->player;
	}
}