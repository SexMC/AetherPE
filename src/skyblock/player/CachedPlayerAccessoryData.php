<?php

declare(strict_types=1);

namespace skyblock\player;

use pocketmine\player\Player;
use pocketmine\Server;
use RedisClient\Pipeline\Version\Pipeline6x0;
use skyblock\Database;
use skyblock\items\accessory\Accessory;
use skyblock\items\accessory\AccessoryItem;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

class CachedPlayerAccessoryData {
	use AwaitStdTrait;

	private string $username;

	/** @var AccessoryItem[] */
	private array $accessories = [];

	private int $extraSlots = 0;

	public function __construct(string|AetherPlayer $player){
		$this->username = strtolower($player instanceof AetherPlayer ? $player->getName() : $player);

		$s = new Session($this->username);
		$this->setAccessories($s->getAccessories());
		$this->setExtraAccessorySlots($s->getExtraAccessorySlots());

		Await::f2c(function() {
			yield $this->getStd()->sleep(60);

			$c = $this->getAccessories();
			$this->accessories = [];
			$this->setAccessories($c);
		});
	}

	public function getAccessories(): array {
		return $this->accessories;
	}

	public function setAccessories(array $accessories) : void{
		$new = $accessories;
		$old = $this->accessories;

		$this->accessories = $accessories;



		$player = $this->getPlayer();

		if($player instanceof AetherPlayer){
			$keyDiff = array_merge(array_diff(array_keys($new), array_keys($old)), array_diff(array_keys($old), array_keys($new)));


			//TODO: remove the player->sendmessage before release
			/** @var AccessoryItem $v */
			foreach($keyDiff as $key){
				if(isset($old[$key]) && !isset($new[$key])){
					$v = $old[$key];
					$v->onDeactivate($player, $v);
					$player->sendMessage(Main::PREFIX . "Deactivated accessory: " . $v->getProperties()->getRarity()->getColor() . $v->getAccessoryName());

					continue;
					//removed
				}


				$v = $new[$key];
				$v->onActivate($player, $v);
				$player->sendMessage(Main::PREFIX . "Activated accessory: " . $v->getProperties()->getRarity()->getColor() . $v->getAccessoryName());
			}
		}
	}

	public function getPlayer(): ?Player {
		return Server::getInstance()->getPlayerExact(substr($this->username, 0, strpos($this->username, "-profile-")));
	}

	public function compare_objects($a, $b) {
		return strcmp(spl_object_hash($a), spl_object_hash($b));
	}

	public function getExtraAccessorySlots(): int {
		return $this->extraSlots;
	}

	public function setExtraAccessorySlots(int $extraSlots) : void{
		$this->extraSlots = $extraSlots;
	}

	public function hasAccessory(AccessoryItem $accessory): bool {
		foreach($this->accessories as $v){
			if($accessory->getAccessoryName() === $v->getAccessoryName()){
				return true;
			}
		}


		return false;
	}

	public function save(?Pipeline6x0 $pipeline = null): void {
		$redis = $pipeline ?? Database::getInstance()->getRedis();

		$redis->set("player.{$this->username}.accessory.collection", json_encode($this->accessories));
		$redis->set("player.{$this->username}.accessory.size", $this->extraSlots);
	}}