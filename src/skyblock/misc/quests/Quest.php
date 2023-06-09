<?php

declare(strict_types=1);

namespace skyblock\misc\quests;

use Closure;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as C;
use skyblock\misc\quests\types\reward\QuestItemReward;
use skyblock\misc\quests\types\reward\QuestReward;

abstract class Quest implements IQuest{

	public int $goal;

	public string $name; //name of the quest must be unique
	/** @var QuestReward[] */
	public array $rewards = [];

	public string $objective; //says what needs to be done in a string in human readable format (needs to be defined by code)

	public string $rewardsText; //rewards in human readable format

	public ?Closure $onFinish;

	public function __construct(int $goal, string $name, string $objective, string $rewardsText, array $rewards = [], ?Closure $onFinish = null){
		$this->goal = $goal;
		$this->name = $name;
		$this->objective = $objective;
		$this->rewards = $rewards;
		$this->rewardsText = $rewardsText;
		$this->onFinish = $onFinish;
	}

	public function getMenuItem(int $progress, int $status): Item {
		$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, $status);

		$color = C::RED;
		$add = C::RED . C::BOLD . "Locked (" . C::RESET . C::GRAY . "Complete previous" . C::BOLD . C::RED . ")";
		if($status === 4){
			$add = C::YELLOW . C::BOLD . "Progress" . C::EOL . C::RESET . C::WHITE . " " . $progress . C::YELLOW . "/" . C::WHITE . $this->goal;
			$color = C::YELLOW;
		} elseif($status === 13){
			$color = C::GREEN;
			$add = C::BOLD . C::GREEN . "Completed";
		}

		$loot = "";

		foreach($this->rewards as $reward){
			if($reward instanceof QuestItemReward){
				$loot .= TextFormat::EOL . "§r§f§l * §r§f{$reward->getCount()}x {$reward->getItem()->getName()}";
			}
		}

		if($loot === "") $loot = "\n" . $this->rewardsText;

		$lore = [
			"",
			C::AQUA . C::BOLD . "Objective",
			C::WHITE . C::RESET . wordwrap($this->objective, 30, "\n" . C::WHITE),
			"",
			C::GREEN . C::BOLD . "Rewards",
			$loot
		];

		$lore[] = "";
		$lore[] = $add;

		$item->setCustomName($color . C::BOLD . $this->name);
		$item->setLore($lore);


		return $item;
	}


	public abstract function getType(): int;

	public abstract function shouldIncreaseProgress($object): int;
}