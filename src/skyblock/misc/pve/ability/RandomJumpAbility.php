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

class RandomJumpAbility extends MobAbility{

	public function attack(Player $player, PveEntity $entity, float $baseDamage) : bool {
		return true;
	}

	public static function getId() : string{
		return "throw";
	}

	public function onTick(PveEntity $entity, int $tick) : void{
		if($tick % 10 === 0 && mt_rand(1, 3) === 1){
			//$entity->jump();
		}
	}

	public function onDeath(PveEntity $entity, EntityDeathEvent $event) : void{}

	public function onDamage(PveEntity $entity, PlayerAttackPveEvent $event) : void{}
}