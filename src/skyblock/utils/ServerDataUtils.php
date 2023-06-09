<?php

declare(strict_types=1);

namespace skyblock\utils;

use skyblock\Database;

class ServerDataUtils {


	public static function getWeeklyLootbox(): int {
		return (int) (Database::getInstance()->getRedis()->get("server.weeklylootbox") ?? 0);
	}

	public static function setWeeklyLootbox(int $slot): void {
		Database::getInstance()->getRedis()->set("server.weeklylootbox", $slot);
	}
}