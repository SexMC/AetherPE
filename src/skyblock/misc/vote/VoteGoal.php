<?php

declare(strict_types=1);

namespace skyblock\misc\vote;

use pocketmine\utils\SingletonTrait;
use skyblock\Database;
use skyblock\Main;

class VoteGoal {
	use SingletonTrait;

	//UPDATING OF THIS IS IN SCOREBOARDUTILS

	public function getVoteGoal(): int {
		return (int) (Database::getInstance()->redisGet("server.votegoal") ?? 0);
	}

	public function setVoteGoal(int $goal): void {
		Database::getInstance()->redisSet("server.votegoal", $goal);
	}

	public function increaseVoteGoal(int $amount = 1): int {
		return Database::getInstance()->getRedis()->incrby("server.votegoal", $amount);
	}
}