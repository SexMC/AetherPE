<?php

declare(strict_types=1);

namespace skyblock\communication;

use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;
use skyblock\communication\operations\BaseOperation;
use Threaded;

class HttpThread extends Thread {



	public function __construct(
		private Threaded $rx,
		private Threaded $tx,
		private SleeperNotifier $notifier,
	){}

	protected function onRun() : void{
		while(!$this->isKilled){

			$operations = [];
			while(($s = $this->rx->shift()) !== null){
				$unserialize = igbinary_unserialize($s);
				if($unserialize instanceof BaseOperation){
					$operations[] = $unserialize;
				}
			}

			foreach($operations as $operation){
				$response = $operation->execute();

				if($operation->getIdentifier() !== null){

					$this->tx[] = igbinary_serialize([$operation->getIdentifier(), $response]);
					$this->notifier->wakeupSleeper();
				}
			}

			usleep(350000);
		}
	}
}