<?php

declare(strict_types=1);

namespace skyblock\misc\coinflip;

use Generator;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use skyblock\communication\CommunicationLogicHandler;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipAddOperation;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipAddRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipGetOperation;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipGetRequestPacket;
use skyblock\communication\packets\types\mechanics\coinflip\CoinflipProgressUpdatePacket;
use skyblock\Main;
use skyblock\misc\quests\IQuest;
use skyblock\misc\quests\QuestHandler;
use skyblock\sessions\Session;
use skyblock\utils\Utils;
use SOFe\AwaitGenerator\Await;
use xenialdan\apibossbar\BossBar;

class CoinflipHandler {
	use SingletonTrait;

	/** @var BossBar[] */
	private array $bossbars = [];

	/**
	 * @return Coinflip[]
	 */
	public function getAllCoinflips(): Generator {
		CommunicationLogicHandler::getInstance()->sendPacket(new CoinflipGetRequestPacket(yield Await::RESOLVE));

		$data = yield Await::ONCE;

		$cfs = [];
		foreach($data as $k => $v){
			$cfs[] = new Coinflip($v["player"], $v["color"], $v["amount"], $v["used"]);
		}

		return $cfs;
	}

	public function addCoinflip(Coinflip $coinflip): Generator {
		CommunicationLogicHandler::getInstance()->sendPacket(new CoinflipAddRequestPacket($coinflip, yield Await::RESOLVE));

		return yield Await::ONCE;
	}

	public function handleIncomingCoinflipData(CoinflipProgressUpdatePacket $pk): void {
		$bossbar = new BossBar();
		$bossbar->setPercentage($pk->progress);

		if($pk->winner !== ""){
			$bossbar->setTitle("§e>>>> Winner: §b{$pk->winner} §e<<<<");
			Utils::executeLater(function() use ($bossbar){
				$bossbar->removeAllPlayers();
			}, 20 * 3);

			if($p = Server::getInstance()->getPlayerExact($pk->winner)){
				QuestHandler::getInstance()->increaseProgress(IQuest::COINFLIP_WIN, $p, new Session($p));
			}
		} else $bossbar->setTitle($pk->title);

		if(isset($this->bossbars[strtolower($pk->opponent)][strtolower($pk->player)]) || isset($this->bossbars[strtolower($pk->player)][strtolower($pk->opponent)])){
			/** @var BossBar $bar */
			$bar = $this->bossbars[strtolower($pk->opponent)][strtolower($pk->player)] ?? $this->bossbars[strtolower($pk->player)][strtolower($pk->opponent)];
			$bar->removeAllPlayers();
		}

		if(($p = Server::getInstance()->getPlayerExact($pk->player))){
			$bossbar->addPlayer($p);
			$this->bossbars[strtolower($p->getName())][strtolower($pk->opponent)] = $bossbar;
		}

		if(($p = Server::getInstance()->getPlayerExact($pk->opponent))){
			$bossbar->addPlayer($p);
			$this->bossbars[strtolower($p->getName())][strtolower($pk->player)] = $bossbar;
		}
	}
}