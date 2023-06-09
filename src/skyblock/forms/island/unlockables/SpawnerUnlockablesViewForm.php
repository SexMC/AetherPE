<?php

declare(strict_types=1);

namespace skyblock\forms\island\unlockables;

use Closure;
use dktapps\pmforms\MenuForm;
use dktapps\pmforms\MenuOption;
use pocketmine\entity\Entity;
use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use skyblock\entity\boss\IslandBossEntity;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\misc\arena\Arena;
use skyblock\misc\arena\ArenaManager;
use skyblock\utils\EntityUtils;
use skyblock\utils\ScoreboardUtils;
use skyblock\utils\TimeUtils;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;

class SpawnerUnlockablesViewForm extends MenuForm {

	public function __construct(private string $id, private Island $island){
		$name = EntityUtils::getEntityNameFromID($this->id);

		parent::__construct(
			EntityUtils::getEntityNameFromID($this->id) . " Spawner",
			"§7To unlock §c$name §7spawners you have to defeat the almighty §c$name BOSS\n\n§7You have 1 hour to defeat the boss.\nAll of your island members can participate in this boss fight",
			$this->getButtons(),
		Closure::fromCallable([$this, "handle"]));
	}


	public function handle(Player $player, int $btn): void {
		$text = strtolower(TextFormat::clean($this->getOption($btn)->getText()));

		if($text === "<- back"){
			$player->sendForm(new SpawnerUnlockablesForm($this->island));
			return;
		}

		if(!Utils::isHubServer()){
			$player->sendMessage(Main::PREFIX . "You can only start boss fights while you're at spawn");
			return;
		}

		if($text === "start boss fight" && ($id = $this->island->getRedis()->get("island.{$this->island->getName()}.boss.arena.id"))){
			if(ArenaManager::getInstance()->getArena($id) !== null){
				$player->sendMessage(Main::PREFIX . "Your island already has an active boss fight going on");
				return;
			}
		}

		if($text === "join boss fight"){
			if(($id = $this->island->getRedis()->get("island.{$this->island->getName()}.boss.arena.id"))){
				if(($arena = ArenaManager::getInstance()->getArena($id))){
					$player->teleport($arena->world->getSpawnLocation());
				}
			}

			return;
		}

		if($text === "cancel boss fight"){
			if(($id = $this->island->getRedis()->get("island.{$this->island->getName()}.boss.arena.id"))){
				if(($arena = ArenaManager::getInstance()->getArena($id))){
					/** @var Entity $boss */
					if(($boss = $arena->fetch("boss")) instanceof IslandBossEntity){
						if(!$boss->isClosed() && !$boss->isFlaggedForDespawn()) {
							$boss->flagForDespawn();
						}
					}

					Await::f2c(function() use($arena, $player) {
						yield Main::getInstance()->getStd()->sleep(1);
						yield ArenaManager::getInstance()->closeArena($arena);

						$this->island->announce("\n§7[§b§lIsland §dBoss§r§7] §c{$player->getName()}§7 has cancelled the boss fight\n\n");
					});
				}
			}

			return;
		}

		Await::f2c(function() use($player){
			$name = EntityUtils::getEntityNameFromID($this->id);


			$onLaunch = function(Arena $arena) use($player, $name): void {
				$location = ArenaManager::getInstance()->getBossSpawnLocation();
				$nbt = new CompoundTag();
				$nbt->setString("networkID38", $this->id);
				$nbt->setInt("hardness", array_search($this->id, SpawnerUnlockablesForm::SPAWNERS) + 1);

				$boss = new IslandBossEntity($this->id, Location::fromObject($location, $arena->world), $nbt);
				$boss->spawnToAll();

				$arena->store("boss", $boss);
				$this->island->announce("\n\n§7[§b§lIsland §dBoss§r§7] §c{$player->getName()} §7has started a §c{$name} §7boss fight!\n[§b§lIsland §dBoss§r§7] §7Type /is unlockables to join the fight!\n\n");
			};

			$onFinish = function(IslandBossEntity $boss, Arena $arena): void {
				$this->island->getRedis()->set("island.{$this->island->getName()}.spawner.{$this->id}", true);
				$this->island->announce("\n§7[§b§lIsland §dBoss§r§7] §7Your island has unlocked §c" . EntityUtils::getEntityNameFromID($this->id) . " spawners§7.\n\n");

			};

			$onTick = function(Arena $arena): void {
				$secondsActive = time() - $arena->createdUnix;
				if($secondsActive > 3600) {
					$this->island->announce("\n§7[§b§lIsland §dBoss§r§7] §7Your island couldn't defeat the §c" . EntityUtils::getEntityNameFromID($this->id) . " Boss §7in time.\n\n");

					Await::f2c(function() use($arena){
						/** @var Entity $boss */
						if(($boss = $arena->fetch("boss")) instanceof IslandBossEntity){
							if(!$boss->isClosed() && !$boss->isFlaggedForDespawn()) {
								$boss->flagForDespawn();
							}
						}

						yield Main::getInstance()->getStd()->sleep(1);
						yield ArenaManager::getInstance()->closeArena($arena);
					});
					return;
				}

				$left = TimeUtils::getFormattedTime(3600 - $secondsActive);
				$health = 0;

				if(($boss = $arena->fetch("boss")) instanceof IslandBossEntity){
					$health = number_format($boss->getHealth());
				}

				foreach($arena->world->getPlayers() as $player){
					ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_BOSS_TIMER, $left);
					ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_BOSS_HEALTH, $health);
				}
			};

			$onWorldEnter = function(Player $player, Arena $arena): void {
				ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_BOSS_OPEN, "");
				ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_BOSS_CLOSE, "");
				ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_BOSS_NAME, EntityUtils::getEntityNameFromID($this->id));
			};

			$onWorldExit = function(Player $player, Arena $arena): void {
				ScoreboardUtils::setStartingScoreboard($player);
			};


			/** @var Arena $arena */
			$arena = yield ArenaManager::getInstance()->createArena($onLaunch, $onFinish, $onTick, $onWorldEnter, $onWorldExit);
			$this->island->getRedis()->set("island.{$this->island->getName()}.boss.arena.id", $arena->id);
			$this->island->getRedis()->set("island.{$this->island->getName()}.boss.arena.boss", $this->id);

			if($player->isOnline()){
				$player->teleport($arena->world->getSpawnLocation());
			}
		});
	}

	public function getButtons(): array {
		$arr = [];

		if($this->island->getRedis()->get("island.{$this->island->getName()}.spawner.{$this->id}") === null){
			if($this->island->getRedis()->get("island.{$this->island->getName()}.boss.arena.boss") === $this->id){
				$id = $this->island->getRedis()->get("island.{$this->island->getName()}.boss.arena.id");
				if($id !== null){
					if(ArenaManager::getInstance()->getArena($id) !== null) {
						$arr[] = new MenuOption("§aJoin Boss Fight");
						$arr[] = new MenuOption("§cCancel Boss Fight");
					}
				}
			}

			if(empty($arr)){
				$arr[] = new MenuOption("§aStart Boss Fight");
			}
		}



		$arr[] = new MenuOption("<- Back");

		return $arr;
	}
}