<?php

declare(strict_types=1);

namespace skyblock\sessions;

use InvalidArgumentException;
use pocketmine\event\world\ChunkPopulateEvent;
use skyblock\items\pets\Pet;
use skyblock\items\pets\PetInstance;
use skyblock\items\potions\AetherPotionInstance;


//IF UPDATING AN ONLINE PLAYERS PET DATA USE AetherPlayer->getPetData()->....
trait SessionPetData {

	/**
	 * @return PetInstance[]
	 */
	public function getPetCollection(): array {
		$raw = $this->getRedis()->get("player.{$this->username}.pets.collection");
		if($raw !== null){
			$arr = json_decode($raw, true);
		} else $arr = [];


		$r = [];
		foreach($arr as $v){
			$pet = PetInstance::fromArray($v);
			$r[$pet->getPet()->getIdentifier()] = $pet;
		}

		return $r;
	}
	
	public function getActivatePotions(): array {
		$potions = [];
		
		$v = $this->getRedis()->get("player.{$this->username}.potions.active") ?? "";

		var_dump("v:", $v);

		if($v === "") return [];

		$data = json_decode($v, true);

		foreach($data as $key => $value){
			$pot = AetherPotionInstance::fromJson($value);

			if($pot->leftDuration > 0){
				$potions[$key] = $pot;
			}
		}

		return $potions;
	}

	public function setActivePotions(array $potions): void {
		$this->getRedis()->set("player.{$this->username}.potions.active", json_encode($potions));
	}

	/**
	 * @param PetInstance[] $collection
	 *
	 * @return void
	 */
	public function setPetCollection(array $collection): void {
		$this->getRedis()->set("player.{$this->username}.pets.collection", json_encode($collection));
	}


	public function getActivePetId(): ?string {
		$v = $this->getRedis()->get("player.{$this->username}.pets.currentId");

		if($v === "") {
			return null;
		}

		return $v;
	}

	public function setActivePetId(?string $id): void {
		$this->getRedis()->set("player.{$this->username}.pets.currentId", $id);
	}
}