<?php

namespace skyblock\misc\quests\types;

use skyblock\misc\quests\Quest;

class VoteQuest extends Quest{

	public function getType() : int{
		return self::VOTE;
	}

	public function shouldIncreaseProgress($object) : int{
		return 1;
	}
}