<?php

declare(strict_types=1);

namespace pathfinder;

use pathfinder\algorithm\AlgorithmSettings;
use pathfinder\algorithm\astar\AStar;
use pathfinder\algorithm\path\PathResult;
use pocketmine\entity\Living;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\io\leveldb\LevelDB;
use pocketmine\world\format\io\WorldProviderManager;
use pocketmine\world\SimpleChunkManager;
use pocketmine\world\World;
use RuntimeException;
use skyblock\items\special\types\HotpotatoBook;
use skyblock\items\tools\types\pve\InkWand;
use Threaded;
use Worker;

class PathfindingThread extends Thread {

	public function __construct(
		private string $worldPath,
		private Threaded $rx,
		private Threaded $tx,
		private SleeperNotifier $notifier
	){ }
	public function buildChunkManager(string $path): ChunkManager {
		$providerManager = new WorldProviderManager();
		$worldProviderManagerEntry = null;
		foreach($providerManager->getMatchingProviders($path) as $worldProviderManagerEntry) {
			break;
		}

		if($worldProviderManagerEntry === null) {
			throw new RuntimeException("Unknown world provider");
		}

		$provider = $worldProviderManagerEntry->fromPath($this->worldPath . DIRECTORY_SEPARATOR);

		if(!$provider instanceof LevelDB) {
			throw new RuntimeException("World provider " . get_class($provider) . " is not supported.");
		}

		$totalChunkCount = $provider->calculateChunkCount();
		var_dump("Loading $totalChunkCount chunks");
		$time = time();
		$manager = new SimpleChunkManager(World::Y_MIN, World::Y_MAX);

		foreach($provider->getAllChunks(true, null) as $coords => $chunk) {
			[$chunkX, $chunkZ] = $coords;
			$manager->setChunk($chunkX, $chunkZ, $chunk->getChunk());
		}
		$took = time() - $time;
		var_dump("All $path chunks loaded in $took seconds");

		$provider->close();

		return $manager;
	}

	protected function onRun() : void{
		$manager = $this->buildChunkManager($this->worldPath);
		$managerFloorOne = $this->buildChunkManager(str_replace("hypixel_net_clone", "hypixel_dungeon_clone", $this->worldPath));



		while(!$this->isKilled){
			while(($raw = $this->rx->shift()) !== null){
				$data = igbinary_unserialize($raw);
				$startVector = $data[0];
				$targetVector = $data[1];
				$closureID = $data[2];
				$time = $data[4];

				$floor = $data[3] ?? null;

				$c = $manager;

				if($floor === 1){
					$c = $managerFloorOne;
				}

				$algo = new AStar($c, $startVector, $targetVector, null,
					(new AlgorithmSettings())
						->setTimeout(0.01)
						->setMaxTicks(0)
				);

				$algo->then(function(?PathResult $result) use($closureID){
					$this->tx[] = igbinary_serialize([$result, $closureID]);
					$this->notifier->wakeupSleeper();
				});


				if(microtime(true) - $time >= 0.2){
					$algo->setRunning(true);
					$algo->stop();
					continue;
				}



				$algo->start();
			}

			usleep(1000);
		}
	}
}