<?php

declare(strict_types=1);

namespace skyblock\logs;

use pocketmine\thread\Thread;
use Threaded;

class LogThread extends Thread {

	private Threaded $rx;

	public function __construct(Threaded $rx){
		$this->rx = $rx;
	}

	protected function onRun() : void{
		while(!$this->isKilled){

			/** @var Log[] $operations */
			$operations = [];
			while(($x = $this->rx->shift()) !== null){
				$data = igbinary_unserialize($x);

				if($data instanceof Log){
					$operations[] = $data;
				}
			}

			foreach($operations as $operation){
				$operation->execute();
			}

			usleep(1000000);
		}
	}
}