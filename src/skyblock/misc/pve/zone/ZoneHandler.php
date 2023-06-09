<?php

declare(strict_types=1);

namespace skyblock\misc\pve\zone;

use pocketmine\math\Vector3;
use pocketmine\utils\Config;
use skyblock\Main;
use skyblock\traits\AetherHandlerTrait;

class ZoneHandler {

	use AetherHandlerTrait;

	private Config $config;

	private array $zoneTypes;
	/** @var Zone[] */
	private array $zones = [];

	public function onEnable() : void{
		$this->config = new Config(Main::getInstance()->getDataFolder() . "zones.json");

		$this->registerZoneTypes();

		$r = Main::getInstance()->getResource("zones.json");
		foreach(json_decode(stream_get_contents($r), true)["zones"] as $k => $v){
			$zone = $this->zoneTypes[$v["type"]] ?? null;

			if($zone === null) {
				Main::debug("Failed to load zone data: " . json_encode($v));
				continue;
			}

			/** @var Zone $class */
			$class = $zone::fromJson($v);

			$this->addZone($class);
		}
	}

	public function getZoneByVector(Vector3 $v): ?Zone {
		foreach($this->zones as $zone){
			if($zone->getBb()->expandedCopy(1, 1, 1)->isVectorInside($v)){
				return $zone;
			}
		}


		return null;
	}


	public function addZone(Zone $zone): void {
		$this->zones[strtolower($zone->getName())] = $zone;
		$this->zonesByType[strtolower($zone->getType())][] = $zone;

		$zone->start();

		Main::debug("Added zone {$zone->getName()}");
	}

	public function removeZone(Zone $zone): void {
		unset($this->zones[strtolower($zone->getName())]);

		$zone->stop();
	}

	public function registerZoneTypes(): void {
		$this->zoneTypes["combat"] = CombatZone::class;
		$this->zoneTypes["fish"] = FishZone::class;
		$this->zoneTypes["foraging"] = ForagingZone::class;
	}

	public function getZone(string $zone): ?Zone {
		return $this->zones[strtolower($zone)] ?? null;
	}

	/**
	 * @return array
	 */
	public function getAllZones() : array{
		return $this->zones;
	}


	public function onDisable() : void{
		/** @var Zone $zone */
		foreach($this->zones as $zone){
			$zone->stop();
		}



		$r = Main::getInstance()->getResource("zones.json");
		$meta_data = stream_get_meta_data($r);
		$filename = $meta_data["uri"];
		file_put_contents($filename, json_encode(["zones" => $this->zones]));
	}
}