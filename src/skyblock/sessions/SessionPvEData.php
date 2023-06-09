<?php

declare(strict_types=1);

namespace skyblock\sessions;

use pocketmine\permission\PermissionManager;
use pocketmine\Server;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\player\PlayerUpdateDataPacket;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\player\ranks\BaseRank;
use skyblock\player\ranks\RankHandler;

trait SessionPvEData {

	public function getHealth(): float {
		$v = $this->getRedis()->get("player.{$this->username}.pve.health") ?? 0;

		if($v === 0) return 100;

		return (float) $v;
	}

	public function setHealth(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.health", $value);
	}

	public function getDefense(): float {
		return (float) $this->getRedis()->get("player.{$this->username}.pve.defense") ?? 1;
	}

	public function setDefense(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.defense", $value);
	}

	public function getStrength(): float {
		return (float) $this->getRedis()->get("player.{$this->username}.pve.strength") ?? 1;
	}

	public function setStrength(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.strength", $value);
	}

	public function getSpeed(): float {
		return (float) $this->getRedis()->get("player.{$this->username}.pve.speed") ?? 10;
	}

	public function setSpeed(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.speed", $value);
	}


	public function getCritChance(): float {
		return (float) $this->getRedis()->get("player.{$this->username}.pve.critChance") ?? 0;
	}

	public function setCritChance(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.critChance", $value);
	}

	public function getCritDamage(): float {
		return (float) $this->getRedis()->get("player.{$this->username}.pve.critDamage") ?? 0;
	}

	public function setCritDamage(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.critDamage", $value);
	}

	public function getIntelligence(): float {
		return (float) $this->getRedis()->get("player.{$this->username}.pve.intelligence") ?? 100;
	}

	public function setIntelligence(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.intelligence", $value);
	}

	public function getSeaCreatureChance(): float {
		return (float) $this->getRedis()->get("player.{$this->username}.pve.seaCreatureChance") ?? 0;
	}

	public function setSeaCreatureChance(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.seaCreatureChance", $value);
	}

	public function setMiningFortune(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.miningFortune", $value);
	}

	public function getMiningFortune(): float{
		return (float) $this->getRedis()->get("player.{$this->username}.pve.miningFortune");
	}

	public function setFarmingFortune(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.farmingFortune", $value);
	}

	public function getFarmingFortune(): float{
		return (float) $this->getRedis()->get("player.{$this->username}.pve.farmingFortune");
	}

	public function setForagingFortune(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.foragingFortune", $value);
	}

	public function getForagingFortune(): float{
		return (float) $this->getRedis()->get("player.{$this->username}.pve.foragingFortune");
	}

	public function setMagicDamage(float $value): void {
		$this->getRedis()->set("player.{$this->username}.pve.magicDamage", $value);
	}

	public function getMagicDamage(): float{
		return (float) $this->getRedis()->get("player.{$this->username}.pve.magicDamage");
	}
}