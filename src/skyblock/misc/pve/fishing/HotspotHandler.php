<?php

declare(strict_types=1);

namespace skyblock\misc\pve\fishing;

use pocketmine\math\Vector3;
use pocketmine\Server;
use skyblock\commands\basic\HotspotCommand;
use skyblock\Main;
use skyblock\misc\pve\zone\FishZone;
use skyblock\misc\pve\zone\ZoneHandler;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class HotspotHandler {
	use AetherHandlerTrait;
	use AwaitStdTrait;

	private ?FishZone $currentHotspot = null;

	private ?HotspotBoost $boost = null;

	public function onEnable() : void{

		Await::f2c(function() {
			while(true){
				$this->rotate();


				yield $this->getStd()->sleep(20 * 60 * 30);
			}
		});
	}

	public function rotate(): void {
		$zones = [];

		foreach(ZoneHandler::getInstance()->getAllZones() as $zone){
			if($zone instanceof FishZone){
				$zones[] = $zone;
			}
		}


		if(count($zones) <= 0) return;

		while($random = $zones[array_rand($zones)]){
			if($random->getName() !== $this->currentHotspot?->getName()){
				$this->currentHotspot = $random;
				$this->boost = new HotspotBoost(mt_rand(10, mt_rand(20, 70)), mt_rand(1, 50), mt_rand(1, 100) / 1000, mt_rand(100, 5000) / 1000);
				break;
			}
		}

        /* DON'T ANNOUNCE THIS
         * Utils::announce([
            "§r",
            "§r§l§b» §r§7Fishing Hotspot Location moved! §r§l§b«",
            "§r§l§b » §r§7New Hotspot Location: §r§c{$this->currentHotspot->getName()} §r§l§b«",
            "§r",
            "§r§l§b» §r§l§3New Hotspot Boosts: §b«",
            "§r§l§b » §r§7/hotspot §r§l§b«",
            "§r",
        ]);*/

		Main::debug("New hotspot selected: " . $this->currentHotspot->getName());
	}

	/**
	 * @return FishZone|null
	 */
	public function getCurrentHotspot() : ?FishZone{
		return $this->currentHotspot;
	}

	/**
	 * @return HotspotBoost|null
	 */
	public function getBoost() : ?HotspotBoost{
		return $this->boost;
	}

	public function isInsideHotspot(Vector3 $vector3): bool {
		if(!$this->currentHotspot) return false;
		if(!$this->boost) return false;

		return $this->currentHotspot->getBb()->expandedCopy(1, 1, 1)->isVectorInside($vector3);
	}
}