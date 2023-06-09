<?php

declare(strict_types=1);

namespace skyblock\misc\kits;

use pocketmine\item\Item;
use pocketmine\player\Player;
use skyblock\items\lootbox\LootboxItem;
use skyblock\Main;
use skyblock\utils\Utils;

class Kit {

	const KIT_TRAVELER = "§f§lTraveler";
	const KIT_SINON = "§e§lSinon";
	const KIT_TROJE = "§3§lTroje";
	const KIT_HYDRA = "§2§lHydra";
	const KIT_THESEUS = "§l§dTheseus";
	const KIT_AURORA = "§l§6Aurora";
	const KIT_AETHER = "§l§bAether";
	const KIT_AETHER_PLUS = "§l§bAether§3+";
	const KIT_ASTRONOMICAL = "§l§5Astronomical";
	const KIT_YOUTUBER = "§c§lYou§fTuber";

	/** @var LootboxItem[] */
	private array $duplicatedItems = [];

	/**
	 * @param string $name
	 * @param array  $items
	 * @param LootboxItem[]  $randItems
	 * @param string $permission
	 * @param int    $cooldown
	 */
	public function __construct(private string $name, private array $items, private array $randItems, private string $permission, private int $cooldown){
		foreach($this->randItems as $basic){
			for($i = 0; $i <= $basic->getChance(); $i++){
				$this->duplicatedItems[] = $basic;
			}
		}
	}
	
	public function give(Player $player): void {
		foreach($this->items as $item){
			Utils::addItem($player, $item);
		}
		
		Utils::addItem($player, $random = $this->getRandomItem());
		
		$player->sendMessage(Main::PREFIX . "You received §c" . $random->getCount() . "x " . $random->getCustomName());
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getItems() : array{
		return $this->items;
	}

	/**
	 * @return int
	 */
	public function getCooldown() : int{
		return $this->cooldown;
	}

	/**
	 * @return string
	 */
	public function getPermission() : string{
		return $this->permission;
	}

	public function getRandomItem(): Item {
		$i =  $this->duplicatedItems[array_rand($this->duplicatedItems)];
		
		return $i->getItem()->setCount(mt_rand($i->getMinCount(), $i->getMaxCount()));
	}
}