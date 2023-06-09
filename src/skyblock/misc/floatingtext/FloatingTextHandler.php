<?php

declare(strict_types=1);

namespace skyblock\misc\floatingtext;

use cosmicpe\floatingtext\handler\FloatingTextFindAndReplaceTickerHandler;
use cosmicpe\floatingtext\handler\FloatingTextHandlerManager;
use skyblock\communication\operations\economy\TopOperation;
use skyblock\Main;
use skyblock\traits\AetherSingletonTrait;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;
use pocketmine\utils\TextFormat as C;

class FloatingTextHandler {
	use AetherSingletonTrait;

	private string $playtime = "loading";
	private string $deaths = "loading";
	private string $kills="loading";
	private string $money = "loading";
	private string $essence = "loading";
	private string $islandValue = "loading";
	private string $islandsPower = "loading";
	private string $farming = "loading";


	public function __construct(){
		self::setInstance($this);

		Utils::executeRepeatedly(function() : void{
			$this->doQueries();
		}, 60 * 20 * 8);
		$this->doQueries();


		Utils::executeLater(function(): void {
			$this->registerAll();
		}, 20 * 3);
	}

	public function doQueries(): void {
		Await::f2c(function() {
			$i = 1;
			$msg = "§7Top §c10 §7wealthiest islands";

			Main::getInstance()->getCommunicationLogicHandler()->addOperation(new TopOperation(TopOperation::TYPE_ISLAND_VALUE, 0, 9, yield Await::RESOLVE));
			$data = yield Await::ONCE;

			if(!isset($data["message"])) return;

			foreach($data["message"] as $d){

				$msg .= C::EOL . "§c#$i - §7" . $d["Player"] . " - §c$" . number_format($d["Value"]);

				$i++;
			}

			$this->islandValue = $msg;
		});

		Await::f2c(function() {
			$i = 1;
			$msg = "§7Top §c10 §7islands with the most power";

			Main::getInstance()->getCommunicationLogicHandler()->addOperation(new TopOperation(TopOperation::TYPE_ISLAND_POWER, 0, 9, yield Await::RESOLVE));
			$data = yield Await::ONCE;

			if(!isset($data["message"])) return;

			foreach($data["message"] as $d){

				$msg .= C::EOL . "§c#$i - §7" . $d["Player"] . " - §c" . number_format($d["Value"]);

				$i++;
			}

			$this->islandsPower = $msg;
		});

		Await::f2c(function() {
			$i = 1;
			$msg = "§7Top §c10 §7richest players";

			Main::getInstance()->getCommunicationLogicHandler()->addOperation(new TopOperation(TopOperation::TYPE_MONEY, 0, 9, yield Await::RESOLVE));
			$data = yield Await::ONCE;

			if(!isset($data["message"])) return;

			foreach($data["message"] as $d){

				$msg .= C::EOL . "§c#$i - §7" . $d["Player"] . " - §c$" . number_format($d["Value"]);

				$i++;
			}

			$this->money = $msg;
		});

		Await::f2c(function() {
			$i = 1;
			$msg = "§7Top §c10 §7players that have the most §cessence";

			Main::getInstance()->getCommunicationLogicHandler()->addOperation(new TopOperation(TopOperation::TYPE_ESSENCE, 0, 9, yield Await::RESOLVE));
			$data = yield Await::ONCE;

			if(!isset($data["message"])) return;

			foreach($data["message"] as $d){

				$msg .= C::EOL . "§c#$i - §7" . $d["Player"] . " - §c" . number_format($d["Value"]);

				$i++;
			}

			$this->essence = $msg;
		});

		Await::f2c(function() {
			$i = 1;
			$msg = "§7Top §c10 §7players with the highest farming level";

			Main::getInstance()->getCommunicationLogicHandler()->addOperation(new TopOperation(TopOperation::TYPE_FARMING_LEVEL, 0, 9, yield Await::RESOLVE));
			$data = yield Await::ONCE;

			if(!isset($data["message"])) return;

			foreach($data["message"] as $d){

				$msg .= C::EOL . "§c#$i - §7" . $d["Player"] . " - §c" . number_format($d["Value"]);

				$i++;
			}

			$this->farming = $msg;
		});
	}

	public function registerAll(): void {
		FloatingTextHandlerManager::register(new FloatingTextFindAndReplaceTickerHandler(Main::getInstance(), "{islandpower}", function (): string {
			return $this->islandsPower;
		}, 60 * 10 * 20));

		FloatingTextHandlerManager::register(new FloatingTextFindAndReplaceTickerHandler(Main::getInstance(), "{islandvalue}", function (): string {
			return $this->islandValue;
		}, 60 * 10 * 20));

		FloatingTextHandlerManager::register(new FloatingTextFindAndReplaceTickerHandler(Main::getInstance(), "{farming}", function (): string {
			return $this->farming;
		}, 60 * 10 * 20));

		FloatingTextHandlerManager::register(new FloatingTextFindAndReplaceTickerHandler(Main::getInstance(), "{essence}", function (): string {
			return $this->essence;
		}, 60 * 10 * 20));

		FloatingTextHandlerManager::register(new FloatingTextFindAndReplaceTickerHandler(Main::getInstance(), "{money}", function (): string {
			return $this->money;
		}, 60 * 10 * 20));
	}
}