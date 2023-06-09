<?php

namespace skyblock\misc\quests\types;

use skyblock\misc\quests\Quest;

class XpSpendQuest extends Quest{

	public function getType() : int{
		return self::XP_SPEND;
	}

	public function shouldIncreaseProgress($object) : int{
		return $object;
	}
}