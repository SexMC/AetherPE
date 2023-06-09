<?php

declare(strict_types=1);

namespace skyblock\misc\recipes;

use pocketmine\block\CraftingTable;
use pocketmine\crafting\CraftingGrid;
use pocketmine\crafting\CraftingManager;
use pocketmine\item\Item;
use skyblock\Main;
use skyblock\player\AetherPlayer;
use skyblock\utils\TimeUtils;
use skyblock\utils\Utils;

class Recipe {

	/** @var Item[] */
	private array $input = [];

	/**
	 * @param string $name
	 * @param Item[]  $inputWithPositions
	 * @param Item   $output
	 * @param int    $craftTime
	 */
	public function __construct(
		private string $name,
		private array $inputWithPositions,
		private Item $output,
		private int $craftTime,
		private bool $autoUnlock = false
	){

		foreach($this->inputWithPositions as $item){
			$item = clone $item;

			$string = json_encode($item);
			if(isset($this->input[$string])){
				$t = $this->input[$string];
				$t->setCount($t->getCount() + $item->getCount());

				$this->input[$string] = $t;
				continue;
			}

			$this->input[$string] = $item;
		}

		$this->input = array_values($this->input);
	}

	public function isAutoUnlocked() : bool{
		return $this->autoUnlock;
	}

	public function getViewItem(): Item {
		$item = clone $this->output;

		$lore = $item->getLore();
		$lore[] = "";
		$lore[] = "§r§7§l§cRequirements";
		foreach($this->input as $input){
			$lore[] = "§r§7§f§l *§r " . Utils::getRandomColor() . $input->getCount() . "x " . $input->getName();
		}


		$item->setLore($lore);
		$item->getNamedTag()->setByte("recipe_view_item", 1);
		$item->getNamedTag()->setString("recipe_name", $this->name);

		return $item;
	}

	/**
	 * @return int
	 */
	public function getCraftTime() : int{
		return $this->craftTime;
	}

	/**
	 * @return Item[]
	 */
	public function getInput() : array{
		return $this->input;
	}

	/**
	 * @return array
	 */
	public function getInputWithPositions() : array{
		return $this->inputWithPositions;
	}


	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return Item
	 */
	public function getOutput() : Item{
		return $this->output;
	}

	public function meetsRequirements(AetherPlayer $player): bool {
		foreach($this->getInput() as $input){
			if(!$player->getInventory()->contains($input)){
				return false;
			}
		}

		return true;
	}
}