<?php

declare(strict_types=1);

namespace skyblock\misc\booster;

use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use skyblock\islands\Island;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\utils\TimeUtils;
use SOFe\AwaitGenerator\Await;
use xenialdan\apibossbar\BossBar;

class BoosterHandler implements Listener {
	use SingletonTrait;
	use AwaitStdTrait;

	public function check(Player $player, Session $session): void {
		Await::f2c(function() use ($player, $session){
			yield $this->getStd()->sleep(1);

			if(($booster = $session->getXpBooster())) {
				$this->startRegularXpBooster($player, $session, $booster);
			}

			yield $this->getStd()->sleep(1);

			if(($booster = $session->getFarmingXpBooster())) {
				$this->startFarmingXpBooster($player, $session, $booster);
			}

			yield $this->getStd()->sleep(1);

			if(($name = $session->getIslandName())){
				$island = new Island($name);

				if(($booster = $island->getXpBooster())){
					$this->startIslandXpBooster($island, $booster);
				}
			}
		});
	}

	public function startRegularXpBooster(Player $player, Session $session, Booster $booster): void {
		Await::f2c(function() use ($player, $session, $booster) {
			$bossbar = new BossBar();
			$bossbar->addPlayer($player);

			while($player->isOnline()) {
				if($booster->getCurrentDuration() <= 0){
					$session->setXpBooster(null);
					break;
				}

				$bossbar->setTitle("§a§l{$booster->getBoost()}x XP Booster §r§7(§c" . TimeUtils::getFormattedTime($booster->getCurrentDuration()) . "§7)");
				$bossbar->setPercentage($this->calculateBossBarPercentage($booster));

				$booster->setCurrentDuration($booster->getCurrentDuration() - 3);
				$session->setXpBooster($booster);

				yield $this->getStd()->sleep(3 * 20);
			}

			$bossbar->removeAllPlayers();
		});
	}

	public function startFarmingXpBooster(Player $player, Session $session, Booster $booster): void {
		Await::f2c(function() use ($player, $session, $booster) {
			$bossbar = new BossBar();
			$bossbar->addPlayer($player);

			while($player->isOnline()) {
				if($booster->getCurrentDuration() <= 0){
					$session->setFarmingXpBooster(null);
					break;
				}

				$bossbar->setTitle("§d§l{$booster->getBoost()}x Farming XP Booster §r§7(§c" . TimeUtils::getFormattedTime($booster->getCurrentDuration()) . "§7)");
				$bossbar->setPercentage($this->calculateBossBarPercentage($booster));

				$booster->setCurrentDuration($booster->getCurrentDuration() - 3);
				$session->setFarmingXpBooster($booster);

				yield $this->getStd()->sleep(3 * 20);
			}

			$bossbar->removeAllPlayers();
		});
	}

	public function startIslandXpBooster(Island $island, Booster $booster): void {
		Await::f2c(function() use ($island, $booster) {
			$bossbar = new BossBar();
			$bossbar->addPlayers($island->getOnlineMembersAsPlayer(true));

			while(true) {
				if($booster->getCurrentDuration() <= 0){
					$island->setXpBooster(null);
					break;
				}

				if(!$island->exists()){
					break;
				}


				$bossbar->setTitle("§b§l{$booster->getBoost()}x Island XP Booster §r§7(§c" . TimeUtils::getFormattedTime($booster->getCurrentDuration()) . "§7)");
				$bossbar->setPercentage($this->calculateBossBarPercentage($booster));

				$booster->setCurrentDuration($booster->getCurrentDuration() - 3);
				$island->setXpBooster($booster);

				yield $this->getStd()->sleep(3 * 20);
			}

			$bossbar->removeAllPlayers();
		});
	}

	public function calculateBossBarPercentage(Booster $booster): float {
		return (100 / $booster->getOriginalDuration() * $booster->getCurrentDuration()) / 100;
	}
}