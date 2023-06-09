<?php

declare(strict_types=1);

namespace skyblock\profile;

use pocketmine\entity\Location;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use Redis;
use RedisClient\Client\Version\RedisClient6x0;
use RedisClient\Pipeline\PipelineInterface;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\server\ServerMessagePacket;
use skyblock\Database;
use skyblock\islands\IslandHandler;
use skyblock\sessions\Session;
use skyblock\utils\ProfileUtils;

class Profile {

	private string $uniqueId;

	public function __construct(string $id){
		$this->uniqueId = $id;
	}


	public static function new(string $owner, array $coops): Profile {
		if(!in_array($owner, $coops)){
			$coops[] = $owner;
		}

		$redis = Database::getInstance()->getRedis();

		$profile = new Profile($id = uniqid());

		$redis->set("profile.{$id}.name", ucwords(IProfile::PROFILE_NAMES[array_rand(IProfile::PROFILE_NAMES)]));
		$redis->sadd("profile.{$id}.members",$coops);
		$redis->sadd("profile.{$id}.owner",$owner);
		$redis->sadd("profile.{$id}.id",$id);
		$redis->set("profile.{$id}.creationUnix", time());



		return $profile;
	}

	public function getProfileSession(): Session {
		return new Session($this->uniqueId);
	}

	public function getPlayerSession(string|Player $coop): ?Session {
		$username = $coop instanceof Player ? $coop->getName() : $coop;


		return new Session($username . "-profile-" .$this->uniqueId);
	}

	public function getFolderOnMount(): string {
		return "/islandworlds/profile-{$this->uniqueId}";
	}

	public function getFolderOnServer(): string {
		return ProfileUtils::getWorldDirectory($this->getUniqueId());
	}

	public function getWorld(): ?World {
		return Server::getInstance()->getWorldManager()->getWorldByName("profile-{$this->uniqueId}");
	}


	public function getRedis(): RedisClient6x0 {
		return Database::getInstance()->getRedis();
	}

	public function isCoopProfile(): bool {
		return count($this->getCoops()) > 1;
	}



	public function getName() : string{
		return $this->getRedis()->get("profile.{$this->uniqueId}.name");
	}

	public function removeCoop(string $username): void {
		$this->getRedis()->srem("profile.{$this->uniqueId}.members", $username);
	}

	public function getCoops() : array{
		return $this->getRedis()->smembers("profile.{$this->uniqueId}.members");
	}

	public function getOwner() : string{
		return $this->getRedis()->get("profile.{$this->uniqueId}.owner");
	}

	public function getCreationUnix(): int {
		return (int) $this->getRedis()->get("profile.{$this->uniqueId}.creationUnix");
	}

	public function getUniqueId() : string{
		return $this->uniqueId;
	}

	public function announce(string|array $msg): void {
		$msg = is_array($msg) ? implode("\n", $msg) : $msg;

		CommunicationLogicHandler::getInstance()->sendPacket(new ServerMessagePacket($msg, $this->getCoops()));
	}
}