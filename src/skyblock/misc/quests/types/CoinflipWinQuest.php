<?php

namespace skyblock\misc\quests\types;

use skyblock\misc\quests\Quest;

class CoinflipWinQuest extends Quest{

	public function getType() : int{
		return self::COINFLIP_WIN;
	}

	public function shouldIncreaseProgress($object) : int{
		return 1;
	}
}