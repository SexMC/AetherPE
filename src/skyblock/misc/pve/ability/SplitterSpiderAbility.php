<?php


declare(strict_types=1);

namespace skyblock\misc\pve\ability;

use pocketmine\entity\Location;
use pocketmine\event\entity\EntityDeathEvent;

use pocketmine\player\Player;

use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;
use skyblock\misc\pve\PveHandler;

class SplitterSpiderAbility extends MobAbility{

	public function attack(Player $player, PveEntity $entity, float $baseDamage) : bool {
		return true;
	}

	public static function getId() : string{
		return "splitter-spider-split";
	}

	public function onTick(PveEntity $entity, int $tick) : void{}

	public function onDeath(PveEntity $entity, EntityDeathEvent $event) : void{
		$loc = $entity->getLocation();

		for($i = 0; $i <= 1; $i++){
			$d = PveHandler::getInstance()->getEntities()["splitter-silverfish"];
			$e = new PveEntity($d["networkID"], Location::fromObject($loc, $loc->getWorld()), $d["nbt"]);
			$e->spawnToAll();
		}
	}

	public function onDamage(PveEntity $entity, PlayerAttackPveEvent $event) : void{}
}