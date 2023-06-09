<?php

declare(strict_types=1);

namespace skyblock\sessions;

use pocketmine\player\Player;
use pocketmine\Server;
use RedisClient\Client\Version\RedisClient6x0;
use RedisClient\Pipeline\PipelineInterface;
use skyblock\caches\playtime\PlayTimeCache;
use skyblock\Database;
use skyblock\islands\Island;
use skyblock\player\AetherPlayer;

class Session {

	private string $username;

	private ?Player $player = null;

	use SessionData;
	use SessionRankData;
	use SessionPvEData;
	use SessionRawData;
	use SessionCollectionData;
	use SessionPetData;
	use SessionAccessoryData;

	public function __construct(string | Player $username) {
		if($username instanceof Player){
			$this->username = strtolower($username->getName());
			$this->player = $username;
		} else $this->username = strtolower($username);
	}

	public function getPlayer(): ?AetherPlayer {
		if($this->player instanceof Player){
			return ($this->player->isOnline() ? $this->player : null);
		}

		if(strpos($this->username, "-profile-") !== false){
			return ($this->player = Server::getInstance()->getPlayerExact(substr($this->username, 0, strpos($this->username, "-profile-"))));

		}

		return ($this->player = Server::getInstance()->getPlayerExact($this->username));
	}

	public function getUsername() : string{
		return $this->username;
	}

	public function create(Player $player, bool $confirm = false): void {
		if($confirm === true){
			$this->setXuid($player->getXuid());

			$this->getRedis()->sadd("players", $player->getName());
		}
	}


	public function getIslandOrNull(): ?Island {
		$name = $this->getIslandName();

		if($name === null){
			return null;
		}

		$island = new Island($name);

		if(!$island->exists()){
			return null;
		}

		return $island;
	}

	public function saveEverything(AetherPlayer $player): void {
		Database::getInstance()->getRedis()->pipeline(function(PipelineInterface $pipeline) use ($player){
			$this->saveInventory($player, null, $pipeline);
			$this->saveArmorInventory($player, null, $pipeline);
			$this->saveEnderchest($player, null, $pipeline);

			if($player->xpLoaded === true){
				$this->setMinecraftXP($player->getXpManager()->getCurrentTotalXp(), $pipeline);
			}

			$this->setPlayTime($this->getPlayTime() + PlayTimeCache::getInstance()->get($player->getName()), $pipeline);
			$this->setQuestData([]  /*todo:$player->quests*/, $pipeline);

			$pipeline->set("player.{$this->username}.questProgress",  0/*TODO $player->questProgress*/);

			$player->getSkillData()->save($pipeline);
			$player->getPveData()->save($pipeline);
			$player->getPetData()->save($pipeline);
			$player->getAccessoryData()->save($pipeline);
			$player->getPotionData()->save($pipeline);

			//$pipeline->set("player.{$this->username}.aetherPotions", json_encode($player->aetherPotions));
		});
	}
}