<?php

declare(strict_types=1);

namespace skyblock\misc\warpspeed;

use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;
use skyblock\commands\basic\AgeCommand;
use skyblock\Database;
use skyblock\Main;
use skyblock\traits\AetherSingletonTrait;

class WarpSpeedHandler implements IWarpSpeed{
	use AetherSingletonTrait;

	/** @var array<string, int> */
	private array $unlocks = [];

	public function __construct(){
		self::setInstance($this);

		$this->register(self::COINFLIP, 1);

		$this->register(self::MASKS, 2);

		$this->register(self::SKIT, 3);

		$this->register(self::MATERIAL_PLANET, 4);
		$this->register(self::MINIONS, 4);

		$this->register(self::RANK_LOOTBOX, 5);
		$this->register(self::HEROIC_UPGRADES, 5);

		$this->register(self::ITEM_MOD_EXPANDERS, 6);
		$this->register(self::HOLY_ESSENCE, 6);
		$this->register(self::NETHER, 6);

		$this->register(self::SPECIAL_WEAPON_SET, 7);
		$this->register(self::KOTH, 7);
		$this->register(self::HEROIC_ENCHANTMENTS, 7);
		$this->register(self::FUND, 7);
	}

	public function sendMessage(CommandSender $player): void {
		$player->sendMessage(Main::PREFIX . "This feature is locked down by warp speed (/warpspeed)");
	}

	public function isUnlocked(string $key): bool {
		if(!isset($this->unlocks[$key])) return true;

		$age = (int) (Database::getInstance()->redisGet("server.age") ?? 0);

		if($age <= 0) return true;

		$age = time() - $age;

		$days = (int) floor($age / 86400);

		return $this->unlocks[$key] <= $days;
	}


	private function register(string $key, int $day): void {
		$this->unlocks[$key] = $day;
	}
}