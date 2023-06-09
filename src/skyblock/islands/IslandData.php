<?php

declare(strict_types=1);

namespace skyblock\islands;

use pocketmine\entity\Location;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\Server;
use RedisClient\Client\Version\RedisClient6x0;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\player\PlayerUpdateDataPacket;
use skyblock\Database;
use skyblock\Main;
use skyblock\misc\booster\Booster;
use skyblock\utils\ScoreboardUtils;
use skyblock\utils\Utils;

trait IslandData {

	public function isDisbanding(): bool {
		return (bool) (Database::getInstance()->redisGet("island.{$this->name}.disbanding") ?? false);
	}

	public function setDisbanding(bool $value): void {
		Database::getInstance()->redisSet("island.{$this->name}.disbanding", $value);
	}

	public function getLimit(string $key): int {
		$limit = (int) ($this->getRedis()->get("island.{$this->name}.limit.$key") ?? 0);

		return ($limit < 0 ? 0 : $limit);
	}

	public function setLimit(string $key, int $value): void {
		$this->getRedis()->set("island.{$this->name}.limit.$key", $value);
	}

	public function increaseLimit(string $key, int $incrBy = 1): int {
		return $this->getRedis()->incrby("island.{$this->name}.limit.$key", $incrBy);
	}

	public function decreaseLimit(string $key, int $decrBy = 1): int {
		return $this->getRedis()->decrby("island.{$this->name}.limit.$key", $decrBy);
	}

	public function setWarp(?Vector3 $vector3): void {
		if($vector3 === null){
			Database::getInstance()->redisSet("island.{$this->name}.warp", null);
		}

		Database::getInstance()->redisSet("island.{$this->name}.warp", "{$vector3->getFloorX()}:{$vector3->getFloorY()}:{$vector3->getFloorZ()}");
	}

	public function setHome(Location $location): void {
		$this->getWorld()?->setSpawnLocation($location->asVector3());
	}

	public function getWarp(): ?Vector3 {
		$data = Database::getInstance()->redisGet("island.{$this->name}.warp");
		if($data === null || $data === "") return null;

		$data = explode(":", $data);

		return new Vector3((int) $data[0], (int) $data[1], (int) $data[2]);
	}

	public function getCaseSensitiveName(): string {
		return Database::getInstance()->redisGet("island.{$this->name}.realname") ?? $this->name;
	}

	public function exists(): bool {
		return Database::getInstance()->redisGet("island.{$this->name}.leader") !== null;
	}

	public function getLeader(): string {
		return Database::getInstance()->redisGet("island.{$this->name}.leader") ?? "";
	}

	public function getIslandUpgrade(string $id): int {
		return (int) ($this->getRedis()->get("island.{$this->name}.upgrade.$id") ?? 0);
	}

	public function setIslandUpgrade(string $id, int $level): void {
		$this->getRedis()->set("island.{$this->name}.upgrade.$id", $level);
	}

	public function increaseIslandUpgrade(string $id, int $level = 1): void {
		$this->getRedis()->incrby("island.{$this->name}.upgrade.$id", $level);
	}

	public function decreaseIslandUpgrade(string $id, int $level = 1): void {
		$this->getRedis()->decrby("island.{$this->name}.upgrade.$id", $level);
	}


	public function getValue(): int {
		return (int) Database::getInstance()->redisGet("island.{$this->name}.value") ?? 0;
	}

	public function increaseValue(int $value) {
		$new = $this->getRedis()->incrby("island.{$this->name}.value", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_VALUE, $new);
			} elseif(Utils::isOnline($member)) {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_VALUE
				));
			}
		}
	}

	public function decreaseValue(int $value) {
		$new = $this->getRedis()->decrby("island.{$this->name}.value", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_VALUE, $new);
			} elseif(Utils::isOnline($member)) {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_VALUE
				));
			}
		}
	}

	public function setValue(int $value) {
		Database::getInstance()->redisSet("island.{$this->name}.value", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_VALUE, $value);
			} elseif(Utils::isOnline($member)) {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_VALUE
				));
			}
		}
	}


	public function getLevel(): int {
		return (int) Database::getInstance()->redisGet("island.{$this->name}.level") ?? 1;
	}

	public function increaseLevel(int $value) {
		$new = $this->getRedis()->incrby("island.{$this->name}.level", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_VALUE, $new);
			} else {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_LEVEL
				));
			}
		}
	}

	public function decreaseLevel(int $value) {
		$new = $this->getRedis()->incrby("island.{$this->name}.level", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_LEVEL, $new);
			} else {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_VALUE
				));
			}
		}
	}

	public function setLevel(int $value) {
		Database::getInstance()->redisSet("island.{$this->name}.level", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_LEVEL, $value);
			} else {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_LEVEL
				));
			}
		}
	}


	public function getMembers(): array {
		return $this->getRedis()->lrange("island.{$this->name}.members", 0, -1);
	}

    public function isMemberOrLeader(Player|string $username): bool {
        if ($username instanceof Player) $username = $username->getName();
        return $this->isLeader($username) || $this->isMember($username);
    }

	public function isMember(Player|string $username): bool {
		return in_array(($username instanceof Player ? $username->getName() : $username), $this->getMembers());
	}

	public function removeMember(string $username): bool {
		return (bool) $this->getRedis()->lrem("island.{$this->name}.members", 1, $username);
	}

	public function addMember(string $username): void {
		$this->getRedis()->lpush("island.{$this->name}.members", $username);
	}

	public function hasPermission(Player|string $member, string $permission): bool {
		$member = strtolower(($member instanceof Player ? $member->getName() : $member));

		return (bool) (Database::getInstance()->redisGet("island.{$this->name}.perms.{$member}.$permission") ?? (IslandInterface::DEFAULT_PERMISSIONS[$permission] ?? false));
	}

	public function setPermission(Player|string $member, string $permission, bool $value): void {
		$member = strtolower(($member instanceof Player ? $member->getName() : $member));

		Database::getInstance()->redisSet("island.{$this->name}.perms.{$member}.$permission", $value);
	}

	public function isLeader(Player | string $player): bool {
		if($player instanceof Player){
			return $player->getName() === $this->getLeader();
		}

		return $this->getLeader() === $player;
	}

	public function setSetting(string $setting, bool $value): void {
		Database::getInstance()->redisSet("island.{$this->name}.setting.{$setting}", $value);
	}

	public function getSetting(string $setting): bool {
		$value = Database::getInstance()->redisGet("island.{$this->name}.setting.{$setting}");

		if($value === null || $value === ""){
			return false;
		}

		return (bool) $value;
	}

	public function setXpBooster(?Booster $booster): void {
		if($booster === null){
			$this->getRedis()->del("island.{$this->name}.xpbooster");
			return;
		}

		Database::getInstance()->redisSet("island.{$this->name}.xpbooster", json_encode($booster));
	}

	public function getXpBooster(): ?Booster {
		$data = Database::getInstance()->redisGet("island.{$this->name}.xpbooster") ?? null;

		if($data === null || $data === ""){
			return null;
		}

		return Booster::jsonDeserialize(json_decode($data, true));
	}

	public function getPower(): int {
		return (int) Database::getInstance()->redisGet("island.{$this->name}.power") ?? 0;
	}

	public function increasePower(int $value) {
		$r = $this->getRedis()->incrby("island.{$this->name}.power", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_POWER, $r);
			} else {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_POWER
				));
			}
		}
	}

	public function decreasePower(int $value) {
		$r = $this->getRedis()->decrby("island.{$this->name}.power", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_POWER, $r);
			} else {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_POWER
				));
			}
		}
	}

	public function setPower(int $value) {
		$r = Database::getInstance()->redisSet("island.{$this->name}.power", $value);

		foreach($this->getOnlineMembers() as $member){
			if($p = Server::getInstance()->getPlayerExact($member)){
				ScoreboardUtils::setLine($p, ScoreboardUtils::LINE_ISLAND_POWER, $r);
			} else {
				CommunicationLogicHandler::getInstance()->sendPacket(new PlayerUpdateDataPacket(
					$member,
					PlayerUpdateDataPacket::UPDATE_BOOSTERS . ScoreboardUtils::LINE_ISLAND_POWER
				));
			}
		}
	}

	public function getRedis(): RedisClient6x0 {
		return Database::getInstance()->getRedis();
	}
}