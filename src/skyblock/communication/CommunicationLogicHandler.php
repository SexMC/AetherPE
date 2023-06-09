<?php

declare(strict_types=1);

namespace skyblock\communication;

use Exception;
use kingofturkey38\vaults38\libs\SOFe\AwaitGenerator\Await;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\lang\Language;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use pocketmine\snooze\SleeperNotifier;
use skyblock\caches\size\IslandBoundingBoxCache;
use skyblock\communication\datatypes\ServerInformation;
use skyblock\communication\operations\BaseOperation;
use skyblock\communication\operations\ClosureStorage;
use skyblock\communication\operations\server\ServerUsagePostOperation;
use skyblock\communication\packets\BasePacket;
use skyblock\communication\packets\PacketRegistry;
use skyblock\communication\packets\types\server\PlayerListRequestPacket;
use skyblock\communication\packets\types\ServerInformationPacket;
use skyblock\events\economy\PlayerCoinflipWinEvent;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\misc\booster\BoosterHandler;
use skyblock\misc\coinflip\CoinflipHandler;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\sessions\Session;
use skyblock\traits\AetherSingletonTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\IslandUtils;
use skyblock\utils\ScoreboardUtils;
use skyblock\utils\Utils;
use Thread;
use Threaded;

class CommunicationLogicHandler{
	use AetherSingletonTrait;
	use AwaitStdTrait;

	public Threaded $rx;
	public Threaded $tx;
	public CommunicationThread $thread;
	public HttpThread $httpThread;
	public Threaded $httpRX;
	public Threaded $httpTX;

	public function __construct(Server $server){
		self::setInstance($this);

		$handler = $server->getTickSleeper();
		$notifier = new SleeperNotifier();

		$this->rx = new Threaded();
		$this->tx = new Threaded();

		$this->thread = new CommunicationThread($this->rx, $this->tx, $notifier, Utils::$isDev);
		$this->thread->setServerInformation(ServerInformation::create());

		$this->thread->start(PTHREADS_INHERIT_NONE);

		$httpNotifier = new SleeperNotifier();
		$this->httpRX = new Threaded();
		$this->httpTX = new Threaded();
		$this->httpThread = new HttpThread($this->httpRX, $this->httpTX, $httpNotifier);
		$this->httpThread->start();

		$handler->addNotifier($notifier, function(){
			while(($s = $this->tx->shift()) !== null){
				$this->onData(igbinary_unserialize($s));
			}
		});

		$handler->addNotifier($httpNotifier, function(){
			while(($s = $this->httpTX->shift()) !== null){
				$data = igbinary_unserialize($s);

				if(isset($data[0]) && isset($data[1])){
					ClosureStorage::executeClosure($data[0], $data[1]);
				}
			}
		});

		$this->onReady();
	}

	public function onData(array $data) : void{
		$class = PacketRegistry::getPacketByType($data["type"]);
		if($class === null) return;

		try{
			$pk = $class::create($data);

			if($pk instanceof BasePacket) {
				$pk->handle($data);
			}
		} catch(Exception $e) {
			var_dump($e->getMessage());
			var_dump($e->getTraceAsString());
		}
	}

	public function onReady() : void{
		Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function() : void{
			$this->sendPacket(new ServerInformationPacket(ServerInformation::create()));
		}), 2, 6);

		Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function() : void{
			$this->sendPacket(new PlayerListRequestPacket());
		}), 2, 10);


		Main::getInstance()->getScheduler()->scheduleDelayedRepeatingTask(new ClosureTask(function() : void{$server = Server::getInstance();

		$this->addOperation(new ServerUsagePostOperation(
				Utils::getServerName(),
				$server->getTickUsageAverage(),
				$server->getTicksPerSecondAverage(),
				count($server->getOnlinePlayers()),
				count(IslandUtils::getLoadedIslands($server))));
		}), 2, 20);

	}

	public function sendPacket(BasePacket $pk): void {
		$this->rx[] = igbinary_serialize($pk);
	}

	public function addOperation(BaseOperation $operation) : void{
		$this->httpRX[] = igbinary_serialize($operation);
	}
}