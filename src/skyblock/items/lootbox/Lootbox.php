<?php

declare(strict_types=1);

namespace skyblock\items\lootbox;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\player\Player;
use skyblock\Database;
use skyblock\items\lootbox\animations\AetherCrateAnimation;
use skyblock\items\lootbox\animations\LootboxAnimation;
use skyblock\logs\LogHandler;
use skyblock\logs\types\LootboxLog;
use skyblock\Main;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\sessions\Session;
use skyblock\traits\AetherSingletonTrait;
use skyblock\utils\Queries;

abstract class Lootbox {

	/** @var LootboxItem[] */
	private array $jackpot = [];
	/** @var LootboxItem[] */
	private array $basic = [];

	private array $nonDuplicatedBasics;

	private array $nonDuplicatedJackpot;

	public function __construct(){
		foreach(($this->nonDuplicatedBasics = $this->getBasicLoot()) as $basic){
			for($i = 0; $i <= $basic->getChance(); $i++){
				$this->basic[] = $basic;
			}
		}

		if($this->isAetherCrate()){
			$this->basic = $this->getBasicLoot(); //cuz those loot is confirmed
		}

		foreach(($this->nonDuplicatedJackpot = $this->getJackpotLoot()) as $jackpot){
			for($i = 0; $i <= $jackpot->getChance(); $i++){
				$this->jackpot[] = $jackpot;
			}
		}
	}

	public static abstract function getName(): string;

	public static abstract function getItem(): Item;

	public function getRandomReward(bool $jackpot = false): LootboxItem {
		if($jackpot){
			if(empty($this->jackpot) === false && $this->getJackpotLootRewardCount() <= 0 && mt_rand(1, 8) === 1){
				return $this->jackpot[array_rand($this->jackpot)];
			}
		}

		return $this->basic[array_rand($this->basic)];
	}

	public function getJackpotReward(): LootboxItem {
		return $this->jackpot[array_rand($this->jackpot)];
	}

	public function open(Player $player): void {
		QuestHandler::getInstance()->increaseProgress(IQuest::OPEN_LOOTBOX, $player, new Session($player), $this);

		if($this->isAetherCrate()){
			(new AetherCrateAnimation($this, $player))->send($player);
			return;
		}

		(new LootboxAnimation($this, $player))->send($player);
	}

	/**
	 * @return LootboxItem[]
	 */
	public function getNonDuplicatedBasics() : array{
		return $this->nonDuplicatedBasics;
	}

	/**
	 * @return LootboxItem[]
	 */
	public function getNonDuplicatedJackpot() : array{
		return $this->nonDuplicatedJackpot;
	}

	/**
	 * @return LootboxItem[]
	 */
	public function getNonDuplicatedAll(): array {
		return array_merge($this->nonDuplicatedJackpot, $this->nonDuplicatedBasics);
	}


	/**
	 * @return LootboxItem[]
	 */
	public function basic() : array{
		return $this->basic;
	}

	/**
	 * @return LootboxItem[]
	 */
	public function jackpot() : array{
		return $this->jackpot;
	}


	public function isAetherCrate(): bool {
		return false;
	}

	public function shouldAnnounce(): bool {
		return true;
	}

	public abstract function getBasicLootRewardCount(): int;

	public function getJackpotLootRewardCount(): int {
		return 0; //if set to 0 and if there's a jackpot loot there's a 1/8 chance that there'll be a jackpot item included
	}

	/**
	 * @return LootboxItem[]
	 */
	protected abstract function getBasicLoot(): array;
	/**
	 * @return LootboxItem[]
	 */
	protected function getJackpotLoot(): array {
		return [];
	}

	protected static function addNametag(Item $item): void {
		$item->getNamedTag()->setString("lootbox", static::getName());
	}

}