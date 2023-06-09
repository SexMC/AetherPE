<?php

declare(strict_types=1);

namespace skyblock\misc\recipes;

use JsonSerializable;
use pocketmine\item\Item;
use skyblock\utils\TimeUtils;

class RecipeCraftingInstance implements JsonSerializable{
	public function __construct(
		public string $recipeName,
		public int $startedUnix,
		public int $slot,
	){ }

	public static function fromData(array $data): self {
		return new self(
			$data["name"],
			$data["unix"],
			$data["slot"],
		);
	}

	public function jsonSerialize(){
		return [
			"name" => $this->recipeName,
			"unix" => $this->startedUnix,
			"slot" => $this->slot,
		];
	}

	public function getViewItem(): ?Item {
		$recipe = RecipesHandler::getInstance()->getRecipe($this->recipeName);
		if($recipe === null) return null;

		$item = clone $recipe->getOutput();
		$lore = $item->getLore();
		$lore[] = "§r";
		if(time() - $this->startedUnix >= $recipe->getCraftTime()){
			$lore[] = "§r§a§lClick to claim";
			$item->getNamedTag()->setByte("recipe_finished_claimable", 1);
		} else {

			$lore[] = "§r§c§lCrafting time";
			$lore[] = "§r§7" . TimeUtils::getFormattedTime($recipe->getCraftTime() - (time() - $this->startedUnix));
		}

		$item->setLore($lore);

		return $item;
	}
}