<?php

declare(strict_types=1);

namespace skyblock\misc\quests;

use JsonSerializable;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIds;
use pocketmine\utils\TextFormat;
use pocketmine\utils\TextFormat as C;
use skyblock\misc\quests\types\reward\QuestItemReward;

class QuestInstance implements JsonSerializable{

	public function __construct(public Quest $quest, public int $progress, public bool $finished){}

	public function jsonSerialize(){
		return [
			$this->getQuestType(),
			$this->quest->name,
			$this->progress,
			$this->finished
		];
	}

	public function getQuestType(): int {
		return IQuest::QUEST_TYPE_NORMAL;
	}
}