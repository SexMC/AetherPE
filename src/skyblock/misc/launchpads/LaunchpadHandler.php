<?php

declare(strict_types=1);

namespace skyblock\misc\launchpads;

use Closure;
use pocketmine\entity\Location;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\math\Vector3;
use pocketmine\Server;
use pocketmine\utils\Config;
use skyblock\CommandHandler;
use skyblock\commands\staff\LaunchPadCommand;
use skyblock\Main;
use skyblock\traits\AetherHandlerTrait;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\EntityUtils;
use SOFe\AwaitGenerator\Await;

class LaunchpadHandler {
	use AetherHandlerTrait;
	use AwaitStdTrait;
	
	private Config $config;

	private array $pads = [];

	private array $fromPositions = [];

	public function onEnable() : void{
		$this->config = new Config(Main::getInstance()->getDataFolder() . "launchpads.json");

		foreach($this->config->get("pads", []) as $pad){
			$object = Launchpad::fromJson($pad);

			$this->addLaunchpad($object);
		}

		Server::getInstance()->getCommandMap()->register("skyblock", new LaunchPadCommand("launchpad"));


		Server::getInstance()->getPluginManager()->registerEvent(
			PlayerMoveEvent::class,
			Closure::fromCallable([$this, "onMove"]),
			EventPriority::HIGHEST,
			Main::getInstance(),
		);
	}

	public function onMove(PlayerMoveEvent $event): void {
		$player = $event->getPlayer();
		if ($event->getFrom()->floor()->asVector3()->equals($event->getTo()->floor()->asVector3())) {
			return;
		}

		$pos = $player->getPosition()->floor()->subtract(0, 1, 0);
		$launchpad = $this->fromPositions[(string) $pos->floor()] ?? null;
		if(!$launchpad instanceof Launchpad){
			return;
		}


		$diff = $launchpad->to->subtractVector($launchpad->from);
		$force = $launchpad->from->distance($launchpad->to) / 9;

		if($force > 8){
			$player->knockBack($diff->x, $diff->z, 8, 2.3);
			Await::f2c(function() use($player, $force, $launchpad) {
				yield $this->getStd()->sleep(10);
				$diff = $launchpad->to->subtractVector($player->getPosition());
				$force = $player->getPosition()->distance($launchpad->to) / 11.8;

				$player->knockBack($diff->x, $diff->z, $force, 1.5);
			});

			return;
		}

		$player->knockBack($diff->x, $diff->z, $force, 2.3);
	}

	public function getLine(Vector3 $from, Vector3 $to, float $addition): array {
		$direction = $to->subtractVector($from);
		$locations = [];

		for($d = $addition; $d < $direction->length(); $d += $addition) {
			$locations[] = (clone $from)->addVector((clone $direction)->normalize()->multiply($d));
		}

		return $locations;
	}

	public function onDisable() : void{
		$this->config->set("pads", $this->pads);
		$this->config->save();
	}

	public function addLaunchpad(Launchpad $launchpad): void {
		$this->pads[$launchpad->name] = $launchpad;
		$this->fromPositions[(string) $launchpad->from->floor()] = $launchpad;

		EntityUtils::spawnTextEntity(Location::fromObject($launchpad->from->floor()->add(0.5, 1, 0.5), Server::getInstance()->getWorldManager()->getDefaultWorld()), "§r§l§a» Launch Pad «", 20 * 99999999);

		Main::debug("Added {$launchpad->name} launchpad");

	}

	/**
	 * @return array
	 */
	public function getPads() : array{
		return $this->pads;
	}

	public function removePad(string $name): void {
		if(isset($this->pads[$name])){
			$pad = $this->pads[$name];
			unset($this->pads[$name]);
			unset($this->fromPositions[(string) $pad->from->floor()]);
		}
	}
}