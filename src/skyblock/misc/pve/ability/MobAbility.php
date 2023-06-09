<?php

declare(strict_types=1);

namespace skyblock\misc\pve\ability;

use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\player\Player;
use skyblock\entity\boss\PveEntity;
use skyblock\events\pve\PlayerAttackPveEvent;

abstract class MobAbility {

	//if this returns false it won't do the default damaging, just hitting the mob. if this returns true it will just do what a regular pve mob do
	abstract public function attack(Player $player, PveEntity $entity, float $baseDamage): bool;

	abstract public function onTick(PveEntity $entity, int $tick): void;
	abstract public function onDeath(PveEntity $entity, EntityDeathEvent $event): void;
	abstract public function onDamage(PveEntity $entity, PlayerAttackPveEvent $event): void;

	public abstract static function getId(): string;
}