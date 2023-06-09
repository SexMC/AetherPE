<?php

declare(strict_types=1);

namespace skyblock\caches\combat;

use JetBrains\PhpStorm\Pure;
use pocketmine\player\Player;
use skyblock\events\player\PlayerCombatEnterEvent;
use skyblock\events\player\PlayerCombatExitEvent;
use skyblock\Main;
use skyblock\sessions\Session;
use skyblock\traits\AwaitStdTrait;
use skyblock\traits\InstanceTrait;
use skyblock\utils\ScoreboardUtils;
use SOFe\AwaitGenerator\Await;

class CombatCache {
	use AwaitStdTrait;
	use InstanceTrait;

	public const COMBAT_TIME = 15;

	private array $cache = [];

	private array $timerCache = [];

	public function __construct(){
		self::$instance = $this;
	}

	public function setInCombat(Player $player, int $seconds = self::COMBAT_TIME, bool $sendMessage = true): void {
		$this->cache[$player->getName()] = $seconds;

		if(!isset($this->timerCache[$player->getName()])){
			$event = new PlayerCombatEnterEvent($player, $seconds);
			$event->call();

			$seconds = $event->getCombatTime();

			if($sendMessage){
				$player->sendMessage(Main::PREFIX . "§7You are now in combat for §c{$seconds} §7seconds!");
			}

			$this->timerCache[$player->getName()] = true;
			Await::f2c(function() use($player, $sendMessage){
				$session = new Session($player);

				while($this->getCombatTimer($player) > 0 && $player->isOnline()){
					ScoreboardUtils::setLine($player, ScoreboardUtils::LINE_COMBAT, "" . $this->cache[$player->getName()]);

					yield $this->getStd()->sleep(20);
					if(isset($this->cache[$player->getName()])){
						$this->cache[$player->getName()]--;
					}
				}

				if(!$session->isStaffMode() && !$player->isCreative()){
					if($player->getAllowFlight()){
						$player->setFlying(false);
						$player->setAllowFlight(false);
					}
				}

				ScoreboardUtils::removeLine($player, $session, ScoreboardUtils::LINE_COMBAT);
				unset($this->timerCache[$player->getName()]);
				unset($this->cache[$player->getName()]);

				(new PlayerCombatExitEvent($player))->call();;

				if($sendMessage){
					$player->sendMessage(Main::PREFIX . "§7You are no longer in combat.");
				}
			});
		}
	}

	#[Pure]
	public function getCombatTimer(Player $player): int {
		return $this->cache[$player->getName()] ?? 0;
	}

	#[Pure]
	public function isInCombat(Player $player): bool {
		return ($this->cache[$player->getName()] ?? 0) > 0;
	}

	public function removeFromCombat(Player $player): void {
		unset($this->cache[$player->getName()]);
		unset($this->timerCache[$player->getName()]);
	}
}