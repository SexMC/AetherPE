<?php

declare(strict_types=1);

namespace skyblock\items\ability;

use pocketmine\item\Item;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\player\AetherPlayer;


class AreaDamageAbility extends ItemAbility {


	public function __construct(private int $range, private float $baseDamage, private bool $isMagic, string $abilityName, int $manaCost, int $cooldown){
		parent::__construct($abilityName, $manaCost, $cooldown);
	}

	protected function execute(AetherPlayer $player, Item $item) : bool{


		$found = false;
		foreach($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy($this->range, $this->range, $this->range)) as $v){
			if($v instanceof PveEntity){
				$e = (new PlayerAttackPveEvent($player, $v, $this->baseDamage));
				$e->setIsMagicDamage($this->isMagic);
				$e->setCause($this);
				$e->call();

				$found = true;
			}
		}

		return $found;
	}
}