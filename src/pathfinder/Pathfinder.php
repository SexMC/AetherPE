<?php

declare(strict_types=1);

namespace pathfinder;

use muqsit\pmhopper\blockscheduler\LoadBalancingBlockScheduler;
use pocketmine\math\Vector3;
use pocketmine\scheduler\AsyncWorker;
use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;
use skyblock\Main;
use skyblock\traits\AetherHandlerTrait;
use SOFe\AwaitGenerator\Await;
use Threaded;

class Pathfinder {
	use AetherHandlerTrait;

	private PathfindingThread $thread;
	private PathfindingThread $thread2;
	private PathfindingThread $thread3;

	private Threaded $rx;
	private Threaded $tx;

	private ?Threaded $rx2;
	private ?Threaded $tx2;

	private ?Threaded $rx3 = null;
	private ?Threaded $tx3;

	public function onEnable() : void{
		$handler = Server::getInstance()->getTickSleeper();
		$notifier = new SleeperNotifier();

		$path = str_replace(
			"hypixel_net",
			"hypixel_net_clone",
			Server::getInstance()->getWorldManager()->getWorldByName("hypixel_net")->getProvider()->getPath()
		);

		$this->thread = new PathfindingThread($path, $this->rx = new Threaded(), $this->tx = new Threaded(), $notifier);
		$this->thread->start();
		/*TODO: disable multiple threads for now cuz of long start times, just only for dev server, it is needed in production: Await::f2c(function() use($path, $notifier, $handler) {
			yield Main::getInstance()->getStd()->sleep(10 * 20);

			$notifier = new SleeperNotifier();


			$this->thread2 = new PathfindingThread($path, $this->rx2 = new Threaded(), $this->tx2 = new Threaded(), $notifier);
			$this->thread2->start();

			$handler->addNotifier($notifier, function(){
				while(($s = $this->tx2->shift()) !== null){
					$raw = igbinary_unserialize($s);
					PathfinderClosureStorage::getInstance()->executeClosure($raw[1], $raw[0]);
				}
			});

			yield Main::getInstance()->getStd()->sleep(10 * 20);

			$notifier = new SleeperNotifier();

			$this->thread3 = new PathfindingThread($path, $this->rx3 = new Threaded(), $this->tx3 = new Threaded(), $notifier);
			$this->thread3->start();

			$handler->addNotifier($notifier, function(){
				while(($s = $this->tx3->shift()) !== null){
					$raw = igbinary_unserialize($s);
					PathfinderClosureStorage::getInstance()->executeClosure($raw[1], $raw[0]);
				}
			});
		});*/

		$handler->addNotifier($notifier, function(){
			while(($s = $this->tx->shift()) !== null){
				$raw = igbinary_unserialize($s);
				PathfinderClosureStorage::getInstance()->executeClosure($raw[1], $raw[0]);
			}
		});
	}

	public function addPending(Vector3 $startVector, Vector3 $targetVector, string $closureID, int $floor = 0): void {
		$data =  igbinary_serialize([$startVector, $targetVector, $closureID, $floor, microtime(true)]);
		if($this->rx3 !== null){
			switch(mt_rand(1, 3)){
				case 1:
					$this->rx[] = $data;
					break;
				case 2:
					$this->rx2[] = $data;
					break;
				case 3:
					$this->rx3[] = $data;
			}
		} else {
			$this->rx[] = $data;
		}
	}
}