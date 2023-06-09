<?php

declare(strict_types=1);

namespace skyblock\events\pve;

use pocketmine\event\Event;
use skyblock\entity\boss\PveEntity;
use skyblock\player\AetherPlayer;

class PveKillPlayerEvent extends Event {

	public function __construct(private AetherPlayer $player, private PveEntity $entity, private PveAttackPlayerEvent $source){ }


	/**
	 * @return AetherPlayer
	 */
	public function getPlayer() : AetherPlayer{
		return $this->player;
	}

	/**
	 * @return PveEntity
	 */
	public function getEntity() : PveEntity{
		return $this->entity;
	}

	/**
	 * @return PveAttackPlayerEvent
	 */
	public function getSource() : PveAttackPlayerEvent{
		return $this->source;
	}
}