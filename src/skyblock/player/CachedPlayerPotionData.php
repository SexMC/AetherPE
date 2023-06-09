<?php

declare(strict_types=1);

namespace skyblock\player;

use pocketmine\player\Player;
use pocketmine\Server;
use RedisClient\Pipeline\Version\Pipeline6x0;
use skyblock\Database;
use skyblock\items\potions\AetherPotionInstance;
use skyblock\items\potions\SkyBlockPotion;
use skyblock\sessions\Session;
use skyblock\utils\Utils;

class CachedPlayerPotionData  {

	/** @var array<string, AetherPotionInstance> */
	private array $potions = [];

	private string $username;

	public function __construct(string|AetherPlayer $player){
		$this->username = strtolower($player instanceof AetherPlayer ? $player->getName() : $player);

		$s = new Session($this->username);

		$this->setActivePotions($s->getActivatePotions());

		Utils::executeLater(function(){
			$p = $this->getPlayer();
			if($p instanceof AetherPlayer){
				foreach($this->getActivePotions() as $potion){
					$potion->item->onActivate($p);
				}
			}
		}, 1);
	}


	public function getActivePotions(): array {
		return $this->potions;
	}

	public function setActivePotions(array $data): void {
		$this->potions = $data;
	}

	public function isActivePotion(SkyBlockPotion $potion): bool {
		return isset($this->potions[$potion->getPotionName()]);
	}

	public function deActivatePotion(SkyBlockPotion $p): void {
		unset($this->potions[$p->getPotionName()]);
	}

	public function save(?Pipeline6x0 $pipeline = null): void {
		$redis = $pipeline ?? Database::getInstance()->getRedis();

		$redis->set("player.{$this->username}.potions.active", json_encode($this->potions));
	}

	public function getPlayer(): ?Player {
		return Server::getInstance()->getPlayerExact(substr($this->username, 0, strpos($this->username, "-profile-")));
	}

	public function __destruct(){
		if(($p = $this->getPlayer()) instanceof AetherPlayer){
			foreach($this->potions as $potion){
				$potion->item->onDeActivate($p);
			}
		}
	}
}
