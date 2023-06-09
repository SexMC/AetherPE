<?php

declare(strict_types=1);

namespace skyblock\items\crates;

use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use skyblock\logs\LogHandler;
use skyblock\logs\types\LootboxLog;
use skyblock\utils\Utils;

abstract class Crate {

	const TAG_GALAXY_KEY = "tag_galaxy_key";

	/** @var CrateItem[] */
	protected array $rewards = [];
	protected array $uniqueRewards = [];

	protected Item $keyItem;

	public function __construct(){
		foreach($this->buildRewards() as $reward){
			for($i = 1; $i <= $reward->getChance(); $i++){
				$this->rewards[] = $reward;
			}

			$this->uniqueRewards[] = $reward;
		}

		$this->keyItem = $this->buildKeyItem()->setCount(1);
		$this->keyItem->getNamedTag()->setString(self::TAG_GALAXY_KEY, $this->getName());
	}

	public function getRandomReward(): Item {
		return $this->rewards[array_rand($this->rewards)]->getItem();
	}

	/**
	 * @return array
	 */
	public function getAllRewards() : array{
		return $this->rewards;
	}

	/**
	 * @return array
	 */
	public function getUniqueRewards() : array{
		return $this->uniqueRewards;
	}

	public function getKeyItem(int $count): Item {
		$item = clone $this->keyItem;
		$item->getNamedTag()->setByte("new", 1);

		return $item->setCount($count);
	}

	public abstract function getName(): string;

	/**
	 * @return CrateItem[]
	 */
	abstract public function buildRewards(): array;

	abstract public function buildKeyItem(): Item;

	public function open(Player $player, Item $item): void {
		if($item->getNamedTag()->getByte("new", 0) === 0){
			$item->setCustomName("OLD KEY");
			$player->getInventory()->setItemInHand($item);
			return;
		}

		$item->pop();
		$player->getInventory()->setItemInHand($item);

		$random = $this->getRandomReward();

		$player->sendMessage("§r§f§l");
		$player->sendMessage("§r§l§f⋆ §r§6{$player->getName()} §bopened a {$this->getName()} crate §band received:");
		$player->sendMessage("§r§l§c⤞ " . $random->getCustomName() . " §r§7" . $random->getCount() . "x");
		$player->sendMessage("§r§f§l");

		Utils::addItem($player, $random);

		LogHandler::getInstance()->log(new LootboxLog($player, $this->getName(), [$random]));
	}
}