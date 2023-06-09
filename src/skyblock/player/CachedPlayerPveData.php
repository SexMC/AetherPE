<?php

declare(strict_types=1);

namespace skyblock\player;

use pocketmine\block\BrewingStand;
use pocketmine\crafting\BrewingRecipe;
use pocketmine\entity\effect\SpeedEffect;
use pocketmine\player\Player;
use pocketmine\Server;
use RedisClient\Pipeline\Version\Pipeline6x0;
use skyblock\Database;

class CachedPlayerPveData {

	private float $health;
	private float $defense;
	private float $strength;
	private float $speed;
	private float $critChance;
	private float $critDamage;
	private float $intelligence;
	private float $seacreatureChance;
	private float $miningFortune;
	private float $farmingFortune;
	private float $foragingFortune;
	private float $magicDamage;
	private string $username;


	private float $maxHealth;
	private float $maxIntelligence;



	//below here will be stats that do not need to be saved but just cached.
	private float $miningWisdom = 0; //At 20 Mining Wisdom, +120 Mining XP would be gained instead of +100 Mining XP.
	private float $combatWisdom = 0; //At 20 Mining Wisdom, +120 Mining XP would be gained instead of +100 Mining XP.
	private float $foragingWisdom = 0; //At 20 Mining Wisdom, +120 Mining XP would be gained instead of +100 Mining XP.
	private float $fishingSpeed = 0;
	private float $miningSpeed = 0;

	public function __construct(Player|string $username){
		$this->username = strtolower($username instanceof Player ? $username->getName() : $username);

		$result = Database::getInstance()->getRedis()->pipeline(function(Pipeline6x0 $pipeline) {
			$pipeline->get("player.{$this->username}.pve.health");
			$pipeline->get("player.{$this->username}.pve.defense");
			$pipeline->get("player.{$this->username}.pve.strength");
			$pipeline->get("player.{$this->username}.pve.speed");
			$pipeline->get("player.{$this->username}.pve.critChance");
			$pipeline->get("player.{$this->username}.pve.critDamage");
			$pipeline->get("player.{$this->username}.pve.intelligence");
			$pipeline->get("player.{$this->username}.pve.seaCreatureChance");
			$pipeline->get("player.{$this->username}.pve.miningFortune");
			$pipeline->get("player.{$this->username}.pve.farmingFortune");
			$pipeline->get("player.{$this->username}.pve.foragingFortune");
			$pipeline->get("player.{$this->username}.pve.magicDamage");
		});

		$this->health = (float) ($result[0] ?? 0) + 100;
		$this->intelligence = (float) ($result[6] ?? 0) + 100;

		$this->setDefense((float) ($result[1] ?? 0) + 1);
		$this->setStrength((float) ($result[2] ?? 0) + 1);
		$this->setSpeed((float) ($result[3] ?? 0) + 100);
		$this->setCritChance((float) $result[4]);
		$this->setCritDamage((float) $result[5]);
		$this->setSeacreatureChance((float) $result[7]);
		$this->setMiningFortune((float) $result[8] ?? 0);
		$this->setFarmingFortune((float) $result[9] ?? 0);
		$this->setForagingFortune((float) $result[10] ?? 0);
		$this->setMagicDamage((float) $result[11] ?? 0);


		$this->setMaxHealth($this->health);
		$this->setHealth($this->getHealth());

		$this->setMaxIntelligence($this->intelligence);
		$this->setIntelligence($this->getIntelligence());
	}
	
	public function save(?Pipeline6x0 $pipeline) {
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.health", $this->health);
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.defense", $this->defense);
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.strength", $this->strength);
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.critChance", $this->critChance);
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.critDamage", $this->critDamage);
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.intelligence", $this->intelligence);
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.seacreatureChance", $this->seacreatureChance);
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.miningFortune", $this->miningFortune);
		//($pipeline ?? Database::getInstance()->getRedis())->set("player.{$this->username}.pve.farmingFortune", $this->farmingFortune);
	}


	public function getMaxHealth() : float{
		return $this->maxHealth;
	}

	public function getMaxIntelligence() : float{
		return $this->maxIntelligence;
	}

	public function getMiningWisdom() : float{
		return $this->miningWisdom;
	}

	public function setMiningWisdom(float $miningWisdom) : void{
		$this->miningWisdom = $miningWisdom;
	}



	public function setMaxHealth(float $maxHealth) : void{
		$this->maxHealth = $maxHealth;
	}


	public function setMaxIntelligence(float $maxIntelligence) : void{
		$this->maxIntelligence = $maxIntelligence;
	}





	public function getHealth() : float{
		return $this->health;
	}


	public function setHealth(float $health) : void{
		$this->health = $health = min($this->getMaxHealth(), $health);

		//TODO: add grinding world check, this is for now for testing
		if($p = $this->getPlayer()){
			$p->setMaxHealth((int) min(20, 20 + (floor($health / 50) * 2)));


			$left = 100 / $this->getMaxHealth() * $health;

			$visibleHealth = 20 * ($left / 100);
			if($visibleHealth > 0){
				$p->setHealth($visibleHealth);
			}
		}
	}


	public function getDefense() : float{
		return $this->defense;
	}


	public function setDefense(float $defense) : void{
		$this->defense = $defense;
	}


	public function getMiningSpeed() : float{
		return $this->miningSpeed;
	}


	public function setMiningSpeed(float $v) : void{
		$this->miningSpeed = $v;
	}


	public function getStrength() : float{
		return $this->strength;
	}


	public function setStrength(float $strength) : void{
		$this->strength = $strength;
	}


	public function getSpeed() : float{
		return $this->speed;
	}


	public function setSpeed(float $speed) : void{
		if($speed === 0.0) return;

		$this->speed = $speed;

		//TODO: add grinding world check, this is for now for testing
		if($p = $this->getPlayer()){
			$p->setMovementSpeed($speed / 1000);
		}
	}

	public function setMagicDamage(float $v) : void{
		$this->magicDamage = $v;
	}

	public function getMagicDamage() : float{
		return $this->magicDamage;
	}

	public function getCritChance() : float{
		return $this->critChance;
	}


	public function setCritChance(float $critChance) : void{
		$this->critChance = $critChance;
	}


	public function getCritDamage() : float{
		return $this->critDamage;
	}


	public function setCritDamage(float $critDamage) : void{
		$this->critDamage = $critDamage;
	}


	public function getIntelligence() : float{
		return $this->intelligence;
	}


	public function setIntelligence(float $intelligence) : void{
		$this->intelligence = min($this->getMaxIntelligence(), $intelligence);
	}


	public function getSeacreatureChance() : float{
		return $this->seacreatureChance;
	}


	public function setSeacreatureChance(float $seacreatureChance) : void{
		$this->seacreatureChance = $seacreatureChance;
	}


	public function getMiningFortune() : float{
		return $this->miningFortune;
	}

	public function setMiningFortune(float $miningFortune) : void{
		$this->miningFortune = $miningFortune;
	}


	public function getFarmingFortune() : float{
		return $this->farmingFortune;
	}

	public function setFarmingFortune(float $farmingFortune) : void{
		$this->farmingFortune = $farmingFortune;
	}


	public function getForagingFortune() : float{
		return $this->foragingFortune;
	}


	public function setForagingFortune(float $foragingFortune) : void{
		$this->foragingFortune = $foragingFortune;
	}

	public function getFishingSpeed() : float{
		return $this->fishingSpeed;
	}

	public function setFishingSpeed(float $fishingSpeed) : void{
		$this->fishingSpeed = $fishingSpeed;
	}



	public function getCombatWisdom() : float{
		return $this->combatWisdom;
	}

	public function setCombatWisdom(float $combatWisdom) : void{
		$this->combatWisdom = $combatWisdom;
	}


	public function getForagingWisdom() : float{
		return $this->foragingWisdom;
	}

	public function setForagingWisdom(float $foragingWisdom) : void{
		$this->foragingWisdom = $foragingWisdom;
	}


	public function getUsername() : string{
		return $this->username;
	}

	public function setUsername(string $username) : void{
		$this->username = $username;
	}

	public function getPlayer(): ?Player {
		return Server::getInstance()->getPlayerExact(substr($this->username, 0, strpos($this->username, "-profile-")));
	}
}