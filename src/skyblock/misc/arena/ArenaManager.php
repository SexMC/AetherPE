<?php

declare(strict_types=1);

namespace skyblock\misc\arena;

use Closure;
use czechpmdevs\multiworld\generator\void\VoidGenerator;
use Generator;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\world\World;
use pocketmine\world\WorldCreationOptions;
use skyblock\Main;
use skyblock\tasks\FileCopyAsyncTask;
use skyblock\tasks\FileDeleteTask;
use skyblock\traits\AetherSingletonTrait;
use skyblock\traits\AwaitStdTrait;
use SOFe\AwaitGenerator\Await;
use Webmozart\PathUtil\Path;

class ArenaManager{

	use AetherSingletonTrait;
	use AwaitStdTrait;

	/** @var array<int, Arena> */
	private array $arenas = [];

	private int $arenaRuntimeID = 0;

	private function createArenaWorld(string $arenaID): Generator {
		$server = Server::getInstance();

		$options = new WorldCreationOptions();
		$options->setGeneratorClass(VoidGenerator::class);
		$options->setSpawnPosition(new Vector3(302, 66, 301));

		$server->getWorldManager()->generateWorld("a-$arenaID", $options, false);

		yield $this->getStd()->sleep(1);
		if($w = $server->getWorldManager()->getWorldByName("a-$arenaID")){
			$server->getWorldManager()->unloadWorld($w);
		}

		$dbPath = Path::join(Server::getInstance()->getDataPath(), "worlds/a-$arenaID/db");
		$server->getAsyncPool()->submitTask(new FileDeleteTask($dbPath, yield Await::RESOLVE));
		yield Await::ONCE;

		$server->getAsyncPool()->submitTask(new FileCopyAsyncTask(Path::join(Main::getInstance()->getDataFolder(), "arena"), $dbPath, yield Await::RESOLVE));
		yield Await::ONCE;

		$server->getWorldManager()->loadWorld("a-$arenaID");

		return $server->getWorldManager()->getWorldByName("a-$arenaID");
	}

	public function createArena(Closure $onLaunch, Closure $onFinish, ?Closure $onTick = null, ?Closure $onWorldEnter = null, ?Closure $onWorldExit = null): Generator {
		$id = uniqid();
		$world = yield $this->createArenaWorld($id);
		$arena = new Arena($id, $world, $onLaunch, $onFinish, $onTick, $onWorldEnter, $onWorldExit, time());

		$onLaunch($arena);
		$this->arenas[$id] = $arena;

		if($onTick !== null){
			Await::f2c(function() use($arena, $onTick){
				while($arena->isActive){
					$onTick($arena);

					yield $this->getStd()->sleep(20);
				}
			});
		}

		return $arena;
	}

	public function getArenaByWorld(World $world): ?Arena {
		foreach($this->arenas as $arena){
			if($arena->world->getDisplayName() === $world->getDisplayName()){
				return $arena;
			}
		}

		return null;
	}

	public function getArena(string $id): ?Arena {
		return $this->arenas[$id] ?? null;
	}

	public function closeArena(Arena $arena): Generator {
		$arena->isActive = false;

		Server::getInstance()->getWorldManager()->unloadWorld($arena->world);

		Server::getInstance()->getAsyncPool()->submitTask(
			new FileDeleteTask(Path::join(Server::getInstance()->getDataPath(), "worlds/a-{$arena->id}"), yield Await::RESOLVE));

		$success = yield Await::ONCE;

		if($success){
			unset($this->arenas[$arena->id]);
		}

		return $success;
	}

	public function getAllArenas(): array {
		return $this->arenas;
	}

	public function closeAll() {
		foreach($this->arenas as $arena){
			$arena->isActive = false;
			Server::getInstance()->getWorldManager()->unloadWorld($arena->world);

			FileDeleteTask::delete(Path::join(Server::getInstance()->getDataPath(), "worlds/a-{$arena->id}"));

			unset($this->arenas[$arena->id]);
		}
	}

	public function getBossSpawnLocation(): Vector3 {
		return new Vector3(260, 67, 260);
	}
}