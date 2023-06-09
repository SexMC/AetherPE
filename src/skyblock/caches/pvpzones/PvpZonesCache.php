<?php

declare(strict_types=1);

namespace skyblock\caches\pvpzones;

use pocketmine\world\Position;
use skyblock\Database;
use skyblock\traits\AetherSingletonTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\InstanceTrait;
use skyblock\utils\Queries;
use SOFe\AwaitGenerator\Await;

class PvpZonesCache{
	use AetherSingletonTrait;
	use AwaitStdTrait;

	/** @var array<string, PvpZone[]> */
	private array $zonesByWorld = [];
	/** @var PvpZone[] */
	private array $zonesByName = [];

	public function __construct(){
		self::setInstance($this);


		Await::f2c(function(){
			while(true){
				yield $this->updateSafezoneCache();
				yield $this->getStd()->sleep(60 * 20);
			}
		});
	}

	public function updateSafezoneCache() {
		$data = yield Database::getInstance()->getLibasynql()->asyncSelect(Queries::SAFEZONES_GET_ALL);

		$this->zonesByName = [];
		$this->zonesByWorld = [];

		foreach($data as $datum){
			$datum["pos1"] = json_decode($datum["pos1"], true);
			$datum["pos2"] = json_decode($datum["pos2"], true);

			$zone = PvpZone::fromJson($datum);
			$this->zonesByWorld[$zone->world][] = $zone;
			$this->zonesByName[$zone->name] = $zone;
		}
	}

	public function getZoneByName(string $name): ?PvpZone {
		return $this->zonesByName[strtolower($name)] ?? null;
	}

	public function getAllZonesByName(): array {
		return $this->zonesByName;
	}

	public function isPvpEnabled(Position $position): bool {
		$zones = $this->zonesByWorld[strtolower($position->getWorld()->getDisplayName())] ?? null;
		if($zones === null) {
			return false;
		}


		foreach($zones as $zone){
			if($zone->bb->isVectorInside($position)){
				return true;
			}
		}

		return false;
	}
}