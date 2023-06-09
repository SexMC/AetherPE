<?php

declare(strict_types=1);

namespace skyblock\islands;

use Generator;
use pocketmine\Server;
use pocketmine\utils\Filesystem;
use pocketmine\world\WorldManager;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\island\IslandSetUnloadingPacket;
use skyblock\Database;
use skyblock\Main;
use skyblock\tasks\FileCopyAsyncTask;
use skyblock\tasks\FileDecompressTask;
use skyblock\tasks\FileDeleteTask;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\IslandUtils;
use skyblock\utils\Queries;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;
use Throwable;
use Webmozart\PathUtil\Path;

class IslandHandler {
	use AetherHandlerTrait;

	use AwaitStdTrait;

	private WorldManager $islandWorldManager;

	public function onEnable() : void{
		$this->islandWorldManager = new WorldManager(Server::getInstance(), "/islandworlds", Server::getInstance()->getWorldManager()->getProviderManager());
	}

	public function getIslandWorldManager() : WorldManager{
		return $this->islandWorldManager;
	}

	public function saveIslandsSynchronously(): void {
		$manager = Server::getInstance()->getWorldManager();
		foreach(IslandUtils::getLoadedIslands() as $is){
			$island = new Island($is);

			Main::debug("force saving island {$island->getName()}");

			$manager->unloadWorld($island->getWorld());

			Filesystem::recursiveCopy($island->getFolderOnServer(), $island->getFolderOnMount());
			FileDeleteTask::delete($island->getFolderOnServer());
			Main::debug("force saved island {$island->getName()}");
		}
	}

	/**
	 * @param string $worldName
	 *
	 * @return Generator<bool>
	 */
	public function loadIsland(string $worldName): Generator {
		$data = yield Database::getInstance()->getLibasynql()->asyncSelect(Queries::WORLDS_GET, ["world" => $worldName]);
		if(isset($data[0])){
			Main::debug("Async selects island world data done");

			$zipPath = self::getWorldDirectory($worldName) . ".zip";
			$outputPath = self::getWorldDirectory($worldName);

			file_put_contents(self::getWorldDirectory($worldName) . ".zip", $data[0]["data"]);

			Main::getInstance()->getServer()->getAsyncPool()->submitTask(new FileDecompressTask($zipPath, $outputPath, yield Await::RESOLVE));
			$result = yield Await::ONCE;
			Main::debug("Island world decompressed");

			if($result === true){
				Main::getInstance()->getServer()->getAsyncPool()->submitTask(new FileDeleteTask($zipPath, yield Await::RESOLVE));
				yield Await::ONCE;
				Server::getInstance()->getWorldManager()->loadWorld("is-$worldName");
				Main::debug("Deleting island world file");

				yield Database::getInstance()->getLibasynql()->asyncGeneric(Queries::WORLDS_DELETE, ["name" => $worldName]);
				return true;
			}
		}

		$island = new Island($worldName);
		Server::getInstance()->getAsyncPool()->submitTask(new FileCopyAsyncTask($island->getFolderOnMount(), $island->getFolderOnServer(), yield Await::RESOLVE));
		$data = yield Await::ONCE;
		if($data === false) return false;

		$manager = Server::getInstance()->getWorldManager();
		$manager->loadWorld("is-$worldName");

		return true;
	}

	/**
	 * @param Island $island
	 * @param bool   $sendUnloadingPacket
	 *
	 * @return Generator<bool>
	 */
	public function saveIsland(Island $island, bool $sendUnloadingPacket = true): Generator {
		try{
			$manager = Server::getInstance()->getWorldManager();

			if(($world = $island->getWorld())){
				foreach($world->getPlayers() as $p){
					$p->sendMessage(Main::PREFIX . "This world is being unloaded, you have been teleported to spawn");
					Utils::hub($p);
				}

				$manager->unloadWorld($world);
			}

			if($sendUnloadingPacket) {
				CommunicationLogicHandler::getInstance()->sendPacket(new IslandSetUnloadingPacket($island->getName(), true));
			}

			if(!is_dir("/islandworlds/is-{$island->getName()}")){
				mkdir("/islandworlds/is-{$island->getName()}");
				mkdir("/islandworlds/is-{$island->getName()}/db");
			}

			Main::debug("Saving island {$island->getName()}");
			Server::getInstance()->getAsyncPool()->submitTask(new FileCopyAsyncTask($island->getFolderOnServer(), $island->getFolderOnMount(), yield Await::RESOLVE));
			yield Await::ONCE;
			Server::getInstance()->getAsyncPool()->submitTask(new FileDeleteTask($island->getFolderOnServer(), yield Await::RESOLVE));
			yield Await::ONCE;
			Main::debug("Saved island {$island->getName()}");


			if($sendUnloadingPacket){
				CommunicationLogicHandler::getInstance()->sendPacket(new IslandSetUnloadingPacket($island->getName(), false));
			}

			return true;
		} catch(Throwable $e) {
			Server::getInstance()->getLogger()->logException($e);
		}

		return false;
	}

	public static function getWorldDirectory(string $worldName): string
	{
		return Path::join(Server::getInstance()->getDataPath(), 'worlds', "is-" . $worldName);
	}
}