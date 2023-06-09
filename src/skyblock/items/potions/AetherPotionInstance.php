<?php

declare(strict_types=1);

namespace skyblock\items\potions;

use JsonSerializable;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\Server;

class AetherPotionInstance implements JsonSerializable{

	public function __construct(
		public string $player,
		public SkyBlockPotion $item,
		public int $leftDuration,
		public int $activatedUnix,
	){ }

	public function jsonSerialize(){
		return [
			"player" => $this->player,
			"item" => $this->item,
			"leftDuration" => $this->leftDuration,
			"activatedUnix" => $this->activatedUnix
		];
	}


	public static function fromJson(array $data): self {
		return new self(
			$data["player"],
			Item::jsonDeserialize($data["item"]),
			$data["leftDuration"],
			$data["activatedUnix"],
		);
	}

	public function getPlayerInstance() : ?Player{
		return Server::getInstance()->getPlayerExact($this->player);
	}
}