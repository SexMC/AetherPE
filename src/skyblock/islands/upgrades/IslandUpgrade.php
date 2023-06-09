<?php

declare(strict_types=1);

namespace skyblock\islands\upgrades;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use skyblock\islands\Island;

abstract class IslandUpgrade {

	/** @var IslandUpgradeLevel[] */
	public array $upgrades = [];


	public function __construct(){
		foreach($this->buildLevels() as $level){
			$this->upgrades[$level->level] = $level;
		}
	}


	abstract public static function getIdentifier(): string;
	abstract public function getName(): string;

	/**
	 * @return IslandUpgradeLevel[]
	 */
	abstract public function buildLevels(): array;
	abstract public function getMenuColor(): string;
	abstract public function getDescription(): array;

	public function onUpgrade(Island $island): void {}

	public function getMaxLevel(): int {
		return count($this->upgrades);
	}

	public function getMenuItem(int $level, int $money, int $regularQuestToken, int $heroicQuestToken, int $essence): Item {
		$item = VanillaItems::EMERALD();

		$item->setCustomName("§r" . $this->getMenuColor() . "§l" . $this->getName());
		$lore = array_map(fn(string $str) => "§r§7$str", $this->getDescription());
		$lore[] = "§r";
		$lore[] = "§r§l" . $this->getMenuColor() . "Tier";
		$lore[] = "§r§l" . $this->getMenuColor() . "$level/" . (count($this->upgrades));
		$lore[] = "§r";
		$lore[] = "§r§l" . $this->getMenuColor() . "Perks";
		foreach($this->upgrades as $upgrade){
			if($upgrade->level <= $level){
				$color = "§a";
			} else $color = "§c";

			$lore[] = "§r{$color}§l* §r$color" . $upgrade->description;
		}

		if($level < count($this->upgrades) && ($next = $this->upgrades[$level + 1]) !== null){
			$lore[] = "§r";
			$lore[] = "§r§l{$this->getMenuColor()}Upgrade Cost";
			if($next->moneyCost > 0){
				$lore[] = "§r{$this->getMenuColor()}§l* §r{$this->getMenuColor()}$" . number_format($next->moneyCost) . " in the Island Bank.§7 (/is deposit)";
				$lore[] = "§r§7(Amount in the bank: §a$" . number_format($money) . "§7) (Need: §c$" . number_format($next->moneyCost) . "§7)";
			}

			if($next->regularQuestTokenCost > 0){
				$lore[] = "§r{$this->getMenuColor()}§l* §r{$this->getMenuColor()}" . number_format($next->regularQuestTokenCost) . " Quest Tokens.§7 (/is deposit)";
				$lore[] = "§r§7(Amount in the bank: §a" . number_format($regularQuestToken) . "§7) (Need: §c" . number_format($next->regularQuestTokenCost) . "§7)";
			}

			if($next->heroicQuestTokenCost > 0){
				$lore[] = "§r{$this->getMenuColor()}§l* §r{$this->getMenuColor()}" . number_format($next->heroicQuestTokenCost) . " Heroic Quest Tokens.§7 (/is deposit)";
				$lore[] = "§r§7(Amount in the bank: §a" . number_format($heroicQuestToken) . "§7) (Need: §c" . number_format($next->heroicQuestTokenCost) . "§7)";
			}

			if($next->essence > 0){
				$lore[] = "§r{$this->getMenuColor()}§l* §r{$this->getMenuColor()}" . number_format($next->essence) . " Essence.§7 (/is deposit)";
				$lore[] = "§r§7(Amount in the bank: §a" . number_format($essence) . "§7) (Need: §c" . number_format($next->essence) . "§7)";
			}

			$lore[] = "§r";
			if($level <= 0){
				$lore[] = "§r§c§lNO OWNED PERKS CLICK TO UPGRADE";
			} else $lore[] = "§r§a§lCLICK TO UPGRADE TO NEXT TIER";
		} else {
			$lore[] = "§r";
			$lore[] = "§r§6§lTHIS ISLAND UPGRADE IS MAXED";
		}

		$item->setLore($lore);
		$item->getNamedTag()->setString("type", static::getIdentifier());

		return $item;
	}
}