<?php

declare(strict_types=1);

namespace skyblock\items\ability;

use pocketmine\item\Item;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\player\AetherPlayer;


class IncreasedDamageAbility extends ItemAbility {

	/**
	 * @param array                $entityIds
	 * @param float                $percentage range 0-1
	 * @param PlayerAttackPveEvent $event
	 * @param string               $abilityName
	 * @param int                  $manaCost
	 * @param int                  $cooldown
	 */
	public function __construct(private array $entityIds, private float $percentage, private PlayerAttackPveEvent $event, string $abilityName, int $manaCost, int $cooldown){
		parent::__construct($abilityName, $manaCost, $cooldown);
	}

	protected function execute(AetherPlayer $player, Item $item) : bool{
		$e = $this->event->getEntity();


		if(in_array($e->getNetworkID(), $this->entityIds)){
			$this->event->multiplyDamage($this->percentage, $this->abilityName);
			return true;
		}

		return false;
	}
}