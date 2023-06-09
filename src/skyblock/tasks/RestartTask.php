<?php

declare(strict_types=1);

namespace skyblock\tasks;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use skyblock\islands\Island;
use skyblock\islands\IslandHandler;
use skyblock\Main;
use skyblock\utils\IslandUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class RestartTask extends Task{
	const RESTART_TIME = 3600 * 8;

	public static int $timeLeft = 3600 * 8;
	private int $startTime;
	public array $announceSeconds = [10, 30, 60, 5, 4, 3, 2, 1];

	public function __construct()
	{
		$this->startTime = time();
	}


	public function onRun(): void
	{
		self::$timeLeft = self::RESTART_TIME - (time() - $this->startTime);

		if(in_array(self::$timeLeft, $this->announceSeconds)){
			if(self::$timeLeft <= 2){
				foreach(Server::getInstance()->getOnlinePlayers() as $player){
					if(Utils::isIslandServer()){
						Utils::hub($player);
					}
				}
			}

			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c" . self::$timeLeft . "§7 seconds on §c" . Utils::getServerName());
		}

		if(self::$timeLeft <= 0){
			Await::f2c(function(){
				foreach(IslandUtils::getLoadedIslands() as $is){
					yield IslandHandler::getInstance()->saveIsland(new Island($is));
				}

				yield Main::getInstance()->getStd()->sleep(20);
				Server::getInstance()->shutdown();
			});

		}

		if(self::$timeLeft === 10800){
			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c3 hours §7 seconds on §c" . Utils::getServerName());
		}

		if(self::$timeLeft === 7200){
			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c2 hours §7 seconds on §c" . Utils::getServerName());
		}

		if(self::$timeLeft === 3600){
			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c1 hours §7 seconds on §c" . Utils::getServerName());
		}

		if(self::$timeLeft === 1800){
			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c30 minutes §7 seconds on §c" . Utils::getServerName());
		}

		if(self::$timeLeft === 900){
			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c15 minutes §7 seconds on §c" . Utils::getServerName());
		}

		if(self::$timeLeft === 600){
			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c10 minutes §7 seconds on §c" . Utils::getServerName());
		}

		if(self::$timeLeft === 300){
			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c5 minutes §7 seconds on §c" . Utils::getServerName());
		}

		if(self::$timeLeft === 150){
			Server::getInstance()->broadcastMessage(Main::PREFIX . "§7Server restart in §c3 minutes §7 seconds on §c" . Utils::getServerName());
		}
	}
}