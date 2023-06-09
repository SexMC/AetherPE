<?php

declare(strict_types=1);

namespace skyblock\player;

use RedisClient\Pipeline\Version\Pipeline6x0;
use skyblock\Database;
use skyblock\items\tools\types\pve\InkWand;
use skyblock\sessions\Session;

class CachedPlayerPetData  {

	private array $pets = [];

	private ?string $activePetId;

	private string $username;

	public function __construct(string|AetherPlayer $player){
		$this->username = strtolower($player instanceof AetherPlayer ? $player->getName() : $player);

		$s = new Session($this->username);
		$this->setPetCollection($s->getPetCollection());
		$this->setActivePetId($s->getActivePetId());

		var_dump("active pet id: " . $s->getActivePetId());
	}


	public function getPetCollection(): array {
		return $this->pets;
	}

	public function setPetCollection(array $pets): void {
		$this->pets = $pets;
	}


	public function getActivePetId() : ?string{
		return $this->activePetId;
	}


	public function setActivePetId(?string $activePetId) : void{
		$this->activePetId = $activePetId;
	}

	public function save(?Pipeline6x0 $pipeline = null): void {
		$redis = $pipeline ?? Database::getInstance()->getRedis();

		$redis->set("player.{$this->username}.pets.collection", json_encode($this->pets));
		$redis->set("player.{$this->username}.pets.currentId", ($this->activePetId === null ? "" : $this->activePetId));
		var_dump($this->activePetId);
	}
}
