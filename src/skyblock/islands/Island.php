<?php

declare(strict_types=1);

namespace skyblock\islands;

use pocketmine\math\AxisAlignedBB;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use RedisClient\Client\Version\RedisClient6x0;
use skyblock\caches\size\IslandBoundingBoxCache;
use skyblock\communication\CommunicationData;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\operations\server\ServerMessageOperation;
use skyblock\communication\packets\types\server\ServerMessagePacket;
use skyblock\Database;
use skyblock\islands\upgrades\types\IslandSizeUpgrade;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\tasks\FileDeleteTask;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Queries;
use SOFe\AwaitGenerator\Await;

class Island implements IslandInterface{
	use AwaitStdTrait;
	use IslandData;
	use IslandBankData;

	public function __construct(private string $name){
		$this->name = strtolower($this->name);
	}

	public function onLoad(): void {
		$this->updateBoundingBoxCache();
	}

	public function disband(bool $confirm = false): void {
		if($confirm === true){
			Await::f2c(function(){
				Main::debug("Disbanding island {$this->name}");
				$start = microtime(true);
				$this->announce(Main::PREFIX . "Island disband process has started.");
				$dir = IslandHandler::getWorldDirectory($this->name);
				Server::getInstance()->getAsyncPool()->submitTask(new FileDeleteTask($dir, yield Await::RESOLVE));
				yield Await::ONCE;

				$redis = $this->getRedis();
				$cursor = 0;
				$keys = [];
				$iteration = 0;

				while(true){
					$iteration++;

					$data = $redis->scan($cursor, "island.{$this->name}.*", 1000000);
					if(!empty($data[1])){
						$keys = array_merge($data[1], $keys);
					}

					$cursor = (int) $data[0];

					if($cursor === 0) break;
					yield $this->getStd()->sleep(1);
				}

				$members = $this->getMembers();
				foreach($members as $m){
					(new Session($m))->setIslandName(null);
				}
				(new Session($leader = $this->getLeader()))->setIslandName(null);

				foreach($keys as $v){
					$redis->del($v);
				}

				yield Database::getInstance()->getLibasynql()->asyncGeneric(Queries::WORLDS_DELETE, ["name" => $this->name]);
				$end = microtime(true) - $start;
				$this->announce(Main::PREFIX . "your island has been disband!", array_merge($members, [$leader]));
				Main::debug("Disband island {$this->name} in {$end}s and $iteration iterations and removed " . count($keys) . " keys");
				//TODO: remove island world and make it so ppl can only disband their island while standing on it
			});
		}
	}

	public function announce(string $message, array $members = null): void {
		CommunicationLogicHandler::getInstance()->sendPacket(new ServerMessagePacket($message, ($members ?: array_merge([$this->getLeader()], $this->getMembers()))));
	}

	public function getWorld(): ?World {
		return Server::getInstance()->getWorldManager()->getWorldByName("is-{$this->name}");
	}

	public function getWorldName(): string {
		return "is-{$this->name}";
	}

	/**
	 * @return string[]
	 */
	public function getOnlineMembers(bool $includeLeader = false): array {
		$online = [];

		foreach($this->getMembers() as $v){
			if(CommunicationData::isOnline($v)){
				$online[] = $v;
			}
		}

		if($includeLeader === true){
			if(CommunicationData::isOnline($l = $this->getLeader())){
				$online[] = $l;
			}
		}

		return $online;
	}

	/**
	 * @return Player[]
	 */
	public function getOnlineMembersAsPlayer(bool $includeLeader = false): array {
		$online = [];

		foreach($this->getMembers($includeLeader) as $v){
			if(CommunicationData::isOnline($v) && ($p = Server::getInstance()->getPlayerExact($v))){
				$online[] = $p;
			}
		}

		return $online;
	}

	public function getBoundingBox(): AxisAlignedBB {
		$bb = IslandBoundingBoxCache::getInstance()->get($this->name);

		if($bb === null){
			$size = 20 + ($this->getIslandUpgrade(IslandSizeUpgrade::getIdentifier()) * 20);
			return new AxisAlignedBB(265 - $size, -1, 265 - $size, 265 + $size, 256, 265 + $size);
		}

		$this->updateBoundingBoxCache();
		return $bb;
	}

	public function updateBoundingBoxCache(): void {
		$size = 20 + ($this->getIslandUpgrade(IslandSizeUpgrade::getIdentifier()) * 20);
		$bb = new AxisAlignedBB(265 - $size, -1, 265 - $size, 265 + $size, 256, 265 + $size);;

		IslandBoundingBoxCache::getInstance()->set($this->name, $bb);
	}

	public function getName(): string {
		return $this->name;
	}

	public function getFolderOnServer(): string {
		return IslandHandler::getWorldDirectory($this->name);
	}

	public function getFolderOnMount(): string {
		return "/islandworlds/is-{$this->name}";
	}
}