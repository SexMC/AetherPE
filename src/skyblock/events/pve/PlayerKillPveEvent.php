<?php

declare(strict_types=1);

namespace skyblock\events\pve;

use pocketmine\event\Event;
use skyblock\entity\boss\PveEntity;
use skyblock\player\AetherPlayer;

class PlayerKillPveEvent extends Event {

	public function __construct(private AetherPlayer $player, private PveEntity $entity, private PlayerAttackPveEvent $source){ }


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
	 * @return PlayerAttackPveEvent
	 */
	public function getSource() : PlayerAttackPveEvent{
		return $this->source;
	}
}