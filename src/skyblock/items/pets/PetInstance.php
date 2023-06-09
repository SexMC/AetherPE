<?php

declare(strict_types=1);

namespace skyblock\items\pets;

use JsonSerializable;
use pocketmine\item\Item;

class PetInstance implements JsonSerializable{

	public function __construct(
		private Pet $pet,
		private int $level,
		private string $uuid,
		private int $rarity,
		private int $xp,
		private int $candyUsed = 0,
	){ }



	public function getLevel() : int{
		return $this->level;
	}

	public function getRarity() : int{
		return $this->rarity;
	}


	public function getPet() : Pet{
		return $this->pet;
	}

	public function getXp() : int{
		return $this->xp;
	}


	public function getUuid() : string{
		return $this->uuid;
	}

	public function buildPetItem(): Item {
		return $this->pet->getItem($this->level, $this->rarity, $this->xp, $this->candyUsed);
	}

	public function setLevel(int $lvl): void {
		$this->level = $lvl;
	}

	public function setXp(int $xp): void {
		$this->xp = $xp;
	}

	public function getCandyUsed() : int{
		return $this->candyUsed;
	}

	public function setCandyUsed(int $candyUsed) : void{
		$this->candyUsed = $candyUsed;
	}


	public static function fromItem(Item $item): ?self {
		$pet = PetHandler::getInstance()->getPet($item->getNamedTag()->getString(IPet::TAG_ID, ""));
		if($pet === null) return null;

		$level = $item->getNamedTag()->getInt(IPet::TAG_LEVEL);
		$uuid = $item->getNamedTag()->getString(IPet::TAG_UUID);
		$rarity = $item->getNamedTag()->getInt(IPet::TAG_RARITY);
		$xp = $item->getNamedTag()->getInt(IPet::TAG_XP);
		$candy = $item->getNamedTag()->getInt(IPet::TAG_CANDY_USED);

		return new PetInstance($pet, $level, $uuid, $rarity, $xp, $candy);
	}

	public function jsonSerialize() : array{
		return [
			"rarity" => $this->rarity,
			"uuid" => $this->uuid,
			"pet" => $this->pet->getIdentifier(),
			"level" => $this->level,
			"xp" => $this->xp,
			"candy" => $this->candyUsed,
		];
	}

	public static function fromArray(array $data): self {
		return new self(PetHandler::getInstance()->getPet($data["pet"]), (int) $data["level"], $data["uuid"], (int) $data["rarity"], (int) ($data["xp"] ?? 0), (int) ($data["candy"] ?? 0));
	}
}