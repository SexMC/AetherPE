<?php


declare(strict_types=1);

namespace skyblock\misc\pve\ability;

use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\types\ActorEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;

class PveMagicImmuneAbility extends MobAbility{

	public function attack(Player $player, PveEntity $entity, float $baseDamage) : bool {
		return true;
	}

	public static function getId() : string{
		return "immune-to-magic-damage";
	}

	public function onTick(PveEntity $entity, int $tick) : void{}

	public function onDeath(PveEntity $entity, EntityDeathEvent $event) : void{}

	public function onDamage(PveEntity $entity, PlayerAttackPveEvent $event) : void{
		if($event->isMagicDamage()){
			$event->setKnockback(0);
			$event->setBaseDamage(0);
			$event->decreaseDamage(1000000, "magic-damage-immune");
			var_dump("here");
		}
	}
}