<?php

declare(strict_types=1);

namespace skyblock\communication;

use Exception;
use pocketmine\snooze\SleeperNotifier;
use pocketmine\thread\Thread;
use skyblock\communication\datatypes\ServerInformation;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\types\CloseConnectionPacket;
use skyblock\communication\packets\types\OpenConnectionPacket;
use Threaded;

class CommunicationThread extends Thread {

	const jsonSeparator = "$0çù(53";

	private bool $isRunning;

	private ?ServerInformation $information = null;

	private Threaded $rx;
	private Threaded $tx;
	private SleeperNotifier $notifier;
	private $isDev;


	public function __construct(Threaded $rx, Threaded $tx, SleeperNotifier $notifier, bool $isDev){
		$this->rx = $rx;
		$this->tx = $tx;
		$this->notifier = $notifier;
		$this->isDev = $isDev;
	}

	protected function onRun() : void{
		while($this->information === null){
			sleep(1);
		}
		/** @var resource $socket */
		$socket = $this->connect();
		$lastResponse = time();

		while($this->isRunning){
			if(time() - $lastResponse >= 5){
				$socket = $this->connect();
				$lastResponse = time();
			}

			while(($pk = $this->rx->shift()) !== null){
				$this->write($socket, igbinary_unserialize($pk));
			}


			$read = @socket_read($socket, 622400);

			if($read !== "" && $read !== false){
				$split = explode(self::jsonSeparator, $read);

				foreach($split as $string){
					$json = json_decode($string, true);

					if(isset($json["type"])){
						$lastResponse = time();

						$this->tx[] = igbinary_serialize($json);
					}
				}

				$this->notifier->wakeupSleeper();
			}

			usleep(15000);
		}

		if($socket !== null){
			$this->write($socket, new CloseConnectionPacket());
			socket_close($socket);
		}
	}

	private function write($socket, BasePacket $pk) {
		try{
			$string = json_encode($pk) . "\n";
			socket_write($socket, $string, strlen($string));

		} catch(Exception $e) {
			var_dump($e->getMessage());
		}

	}

	private function connect() {
		echo "\n\nTries to connect to server\n\n";

		do {
			if(!$this->isRunning) {
				return null;
			}
			$socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		} while (!$socket);

		do {
			if(!$this->isRunning) {
				return null;
			}
			$port = ($this->isDev === true ? 8089 : 8088);

			$connected = @socket_connect($socket, "135.148.150.31", $port);
			if (!$connected) {
				sleep(5);
			}
		} while (!$connected);
		socket_set_nonblock($socket);

		$this->write($socket, new OpenConnectionPacket($this->information));
		echo "\n\nConnected to socket server\n\n";

		return $socket;
	}

	public function setIsRunning(bool $isRunning) : void{
		$this->isRunning = $isRunning;
	}

	public function start(int $options = PTHREADS_INHERIT_NONE) : bool{
		$this->isRunning = true;
		return parent::start($options);
	}

	public function setServerInformation(ServerInformation $information): void {
		$this->information = $information;
	}

	public function quit() : void{
		$this->isRunning = false;
		parent::quit();
	}
}