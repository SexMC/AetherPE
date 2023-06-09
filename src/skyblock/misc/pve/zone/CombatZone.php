<?php

declare(strict_types=1);

namespace skyblock\misc\pve\zone;

use pocketmine\block\Air;
use pocketmine\entity\Location;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;
use pocketmine\Server;
use skyblock\entity\boss\PveEntity;
use skyblock\misc\pve\PveHandler;
use skyblock\player\AetherPlayer;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;

class CombatZone extends Zone {
	use AwaitStdTrait;

	private bool $running = false;

	private array $cache = [];

	public function __construct(string $name, AxisAlignedBB $bb, private array $mobs){
		$this->type = "combat";
		$this->name = $name;
		$this->bb = $bb;
	}

	public function start() : void{
		$this->running = true;

		Await::f2c(function() {
			$world = PveHandler::getInstance()->getPveWorld();

			while($this->running){
				yield $this->getStd()->sleep(mt_rand(20, 30));

				$nearbyEntities = $world->getNearbyEntities($this->bb->expandedCopy(15, 15, 15));
				$found = false;
				foreach($nearbyEntities as $entity){
					if($entity instanceof AetherPlayer){
						$found = true;
						break;
					}
				}

				if(!$found) continue;

				foreach($this->mobs as $mob){
					$name = $mob[0];
					$max = $mob[1];
					$interval = $mob[2];

					if(!isset($this->cache[$name])){
						$this->spawnMob($name);

						continue;
					}

					$data = $this->cache[$name];

					if($data["count"] >= $max) continue;
					if(time() - $data["lastSpawn"] < $interval) continue;

					$this->spawnMob($name);
				}
			}
		});
	}

	protected function spawnMob(string $mob): void {
		$data = $this->cache[$mob] ?? ["count" => 0, "lastSpawn" => time()];

		$data["count"]++;
		$data["lastSpawn"] = time();
		$this->cache[$mob] = $data;

		$d = PveHandler::getInstance()->getEntities()[$mob];
		$e = new PveEntity($d["networkID"], Location::fromObject($this->getRandomSpawnVector()->add(0.5, 0, 0.5), PveHandler::getInstance()->getPveWorld()), $d["nbt"]);
		$e->setZone($this);
		$e->spawnToAll();
	}

	public function decreaseMob(string $mob): void {
		if(!isset($this->cache[$mob])) return;

		$this->cache[$mob]["count"] -= 1;
	}

	protected function getRandomSpawnVector(): Vector3 {
		$vector = $this->getRandomVector();

		$world = PveHandler::getInstance()->getPveWorld();
		while(!$world->getBlockAt($vector->x, $vector->y, $vector->z) instanceof Air){
			if($y = $world->getHighestBlockAt($vector->x, $vector->z)){
				if($this->bb->isVectorInside($new = $vector->withComponents(null, $y + 1, null))){
					$vector = $new;
					continue;
				}
			}

			$vector = $this->getRandomVector();
		}

		return $vector;
	}

	protected function getRandomVector(): Vector3 {
		return new Vector3(
			mt_rand((int) $this->bb->minX, (int) $this->bb->maxX),
			mt_rand((int) $this->bb->minY, (int) $this->bb->maxY),
			mt_rand((int) $this->bb->minZ, (int) $this->bb->maxZ),
		);
	}

	public function stop() : void{
		$this->running = false;
	}


	public static function fromJson(array $data) : Zone{
		return new CombatZone(
			$data["name"],
			new AxisAlignedBB(...$data["aabb"]),
			$data["mobs"],
		);
	}

	public function addMob(string $mob, int $max, int $spawnInterval): void {
		$this->mobs[] = [$mob, $max, $spawnInterval];
	}

	/**
	 * @return array
	 */
	public function getMobs() : array{
		return $this->mobs;
	}

	public function removeMob(string $mob): void {
		unset($this->mobs[array_search($mob, $this->mobs)]);
	}

	public function jsonSerialize(){
		$data = parent::jsonSerialize();

		$data["mobs"] = $this->mobs;

		return $data;
	}
}