<?php

namespace skyblock\misc\quests\types;

use skyblock\misc\quests\Quest;

class FishQuest extends Quest{

	public function getType() : int{
		return self::FISH;
	}

	public function shouldIncreaseProgress($object) : int{
		return 1;
	}
}