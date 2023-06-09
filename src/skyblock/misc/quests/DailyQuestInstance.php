<?php

declare(strict_types=1);

namespace skyblock\misc\quests;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;
use skyblock\misc\quests\types\reward\QuestItemReward;
use skyblock\utils\TimeUtils;

class DailyQuestInstance extends QuestInstance{

	public int $expiringUnix;

	public function __construct(Quest $quest, int $progress, bool $finished, int $expiringUnix){
		$this->quest = $quest;
		$this->progress = $progress;
		$this->finished = $finished;
		$this->expiringUnix = $expiringUnix;
	}

	public function isExpired(): bool {
		return time() >= $this->expiringUnix;
	}

	public function jsonSerialize(){
		$data = parent::jsonSerialize();
		$data[] = $this->expiringUnix;

		return $data;
	}

	public function getMenuItem(): Item {
		$meta = $this->finished ? 5 : 14;

		$item = ItemFactory::getInstance()->get(ItemIds::STAINED_GLASS_PANE, $meta);
		$loot = "";

		foreach($this->quest->rewards as $reward){
			if($reward instanceof QuestItemReward){
				$loot .= TextFormat::EOL . "§r§f§l * §r§f{$reward->getCount()}x {$reward->getItem()->getCustomName()}";
			}
		}

		if($loot === "") $loot = "\n" . $this->quest->rewardsText;

		if(time() >= $this->expiringUnix){
			$left = "§cExpired§r§7";
		} else $left = TimeUtils::getFormattedTime($this->expiringUnix - time());

		$progress = number_format($this->progress);
		$goal = number_format($this->quest->goal);
		$progress = $this->finished === false ? "§cUnfinished ({$progress}/{$goal})" : "§aFinished";

		$item->setCustomName("§r" . ($this->finished ? "§a" : "§c") . $this->quest->name);
		$item->setLore([
			"§r§l§fTask",
			"§r§l§f * §r§a{$this->quest->objective}",
			"§r",
			"§r§f§lLoot$loot",
			"§r",
			"§r§f§lStatus",
			"§r§l§f * §r§c$progress",
			"§r",
			"§r§7You have $left to finish",
			"§r§7this quest, if it remains unfinished, a new quest will be offered.",
		]);

		$item->getNamedTag()->setString("unique_id", uniqid());


		return $item;
	}

	public function getQuestType(): int {
		return IQuest::QUEST_TYPE_DAILY;
	}
}