<?php

declare(strict_types=1);

namespace skyblock\utils;

use Closure;
use jackmd\scorefactory\ScoreFactory;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;
use pocketmine\Server;
use skyblock\islands\Island;
use skyblock\Main;
use skyblock\misc\vote\VoteGoal;
use skyblock\player\AetherPlayer;
use skyblock\sessions\Session;
use SOFe\AwaitGenerator\Await;

class ScoreboardUtils {

	const SCOREBOARD_DEFAULT = 0;
	const SCOREBOARD_BOSS = 2;
	const SCOREBOARD_KOTH = 1;
	const SCOREBOARD_STRONGHOLD = 3;

	const LINE_ISLAND_TEXT = "island_text";
	const LINE_ISLAND_LEVEL = "island_level";
	const LINE_ISLAND_VALUE = "island_value";
	const LINE_ISLAND_POWER = "island_power";
	const LINE_PERSONAL_TEXT = "personal text";
	const LINE_PERSONAL_MONEY = "personal_money";
	const LINE_PERSONAL_ESSENCE = "personal_essence";
	const LINE_PERSONAL_XP = "personal_xp";
	const LINE_VOTE_GOAL = "vote_goal";
	const LINE_PERSONAL_CLOSE = "personal_close";
	const LINE_BOSS_TEXT = "boss_text";
	const LINE_BOSS_NAME = "boss_name";
	const LINE_BOSS_HEALTH = "boss_health";
	const LINE_BOSS_TIMER = "boss_timer";
	const LINE_BOSS_CLOSE = "boss_close";
	const LINE_COMBAT = "combat";
	const LINE_IP = "ip";

	const LINE_KOTH_OPEN = "koth_open";
	const LINE_KOTH_CAPTURER = "koth_capturer";
	const LINE_KOTH_TIME = "koth_time";
	const LINE_KOTH_TYPE = "koth_type";
	const LINE_KOTH_CLOSE = "koth_close";

	const LINE_BOSS_OPEN = "boss_open";


	const LINE_STRONGHOLD_OPEN = "stronghold_open";
	const LINE_STRONGHOLD_CONTROLLED_BY = "stronghold_controlled_by";
	const LINE_STRONGHOLD_ATTACKERS = "stronghold_attackers";
	const LINE_STRONGHOLD_PERCENTAGE = "stronghold_percentage";
	const LINE_STRONGHOLD_STATUS = "stronghold_status";
	const LINE_STRONGHOLD_CLOSE = "stronghold_close";

	private static array $closure = [];

	private static array $cache = [];

	private static int $lastVoteGoal = -1;

	public static function init(): void {
		self::addCallback(self::LINE_IP, fn(Player $player, Session $session, $value) : array => [0, "§r§d§o     aetherpe.net"]);
		self::addCallback(self::LINE_ISLAND_TEXT, fn(Player $player, Session $session, $value) : array => [15, "§5┏—–§r§d Island"]);
		/*self::addCallback(self::LINE_ISLAND_LEVEL, function(Player $player, Session $session, $value) : array {
			if($value === null){
				if(($is = $session->getIslandName()) !== null){
					$value = (new Island($is))->getLevel();
				} else $value = 0;
			}

			return [14, "§5|§r  §fLevel: §7$value"];
		});*/

		self::addCallback(self::LINE_ISLAND_VALUE, function(Player $player, Session $session, $value): array {
			if($value === null){
				if(($is = $session->getIslandName()) !== null){
					$value = (new Island($is))->getValue();
				} else $value = 0;
			}

			return [14, "§5|§r  §fValue: §7" . number_format((int)$value)];
		});

		self::addCallback(self::LINE_ISLAND_POWER, function(Player $player, Session $session, $value): array {
			if($value === null){
				if(($is = $session->getIslandName()) !== null){
					$value = (new Island($is))->getPower();
				} else $value = 0;
			}

			return [13, "§5|§r  §fPower: §7" . number_format((int)$value)];
		});
		
		self::addCallback(self::LINE_PERSONAL_TEXT, fn(Player $player, Session $session, $value) : array => [12, " "]);
		self::addCallback(self::LINE_PERSONAL_MONEY, fn(Player $player, Session $session, $value) : array => [11, "§5 §r  §fPurse: §7" . number_format((int) ($value === null ? $session->getPurse() : $value))]);
		self::addCallback(self::LINE_PERSONAL_ESSENCE, fn(Player $player, Session $session, $value) : array => [10, "§5 §r  §fGems: §7" . number_format((int) ($value === null ? $session->getEssence() : $value))]);
		self::addCallback(self::LINE_PERSONAL_XP, fn(Player $player, Session $session, $value) : array => [9, "§5 §r  §fBits: §7" . number_format((int) ($value === null ? $player->getXpManager()->getCurrentTotalXp() : $value))]);
		self::addCallback(self::LINE_VOTE_GOAL, fn(Player $player, Session $session, $value): array => [8, "§5 §r  §fVote Goal: §7" . $value . "/100"]);
		self::addCallback(self::LINE_PERSONAL_CLOSE, fn(Player $player, Session $session, $value): array => [7, "  "]);
		self::addCallback(self::LINE_COMBAT, fn(Player $player, Session $session, $value): array => [1, "§eCombat§8 | §6" . ($value) . "s"]);

		self::addCallback(self::LINE_KOTH_OPEN, fn(Player $player, Session $session, $value): array => [6, "§5┠—–§r§d KoTH"]);
		self::addCallback(self::LINE_KOTH_CAPTURER, fn(Player $player, Session $session, $value): array => [5, "§5| §r§f Capturing:§7 $value"]);
		self::addCallback(self::LINE_KOTH_TIME, fn(Player $player, Session $session, $value): array => [4, "§5| §r§f Timer:§7 $value"]);
		self::addCallback(self::LINE_KOTH_TYPE, fn(Player $player, Session $session, $value): array => [3, "§5| §r§f Mode:§7 $value"]);
		self::addCallback(self::LINE_KOTH_CLOSE, fn(Player $player, Session $session, $value): array => [2, "§5┗—–"]);

		self::addCallback(self::LINE_BOSS_OPEN, fn(Player $player, Session $session, $value): array => [6, "§5┠—–§r§d Boss"]);
		self::addCallback(self::LINE_BOSS_NAME, fn(Player $player, Session $session, $value): array => [5, "§5| §r§f Boss:§7 $value"]);
		self::addCallback(self::LINE_BOSS_HEALTH, fn(Player $player, Session $session, $value): array => [4, "§5| §r§f Health:§7 $value"]);
		self::addCallback(self::LINE_BOSS_TIMER, fn(Player $player, Session $session, $value): array => [3, "§5| §r§f Time Left:§7 $value"]);
		self::addCallback(self::LINE_BOSS_CLOSE, fn(Player $player, Session $session, $value): array => [2, "§5┗—–"]);

		self::addCallback(self::LINE_STRONGHOLD_OPEN, fn(Player $player, Session $session, $value): array => [7, "§5┠—–§r§d Stronghold $value"]);
		self::addCallback(self::LINE_STRONGHOLD_CONTROLLED_BY, fn(Player $player, Session $session, $value): array => [6, "§5| §r§f Controlled By:§7 $value"]);
		self::addCallback(self::LINE_STRONGHOLD_ATTACKERS, fn(Player $player, Session $session, $value): array => [5, "§5| §r§f Attackers:§7 $value"]);
		self::addCallback(self::LINE_STRONGHOLD_PERCENTAGE, fn(Player $player, Session $session, $value): array => [4, "§5| §r§f Percentage:§7 $value"]);
		self::addCallback(self::LINE_STRONGHOLD_STATUS, fn(Player $player, Session $session, $value): array => [3, "§5| §r§f Status:§7 $value"]);
		self::addCallback(self::LINE_STRONGHOLD_CLOSE, fn(Player $player, Session $session, $value): array => [2, "§5┗—–"]);

		Await::f2c(function() {
			while(true){
				$new = VoteGoal::getInstance()->getVoteGoal();
				if($new !== self::$lastVoteGoal){
					self::$lastVoteGoal = $new;

					foreach(Server::getInstance()->getOnlinePlayers() as $player){
						self::setLine($player, self::LINE_VOTE_GOAL, $new);
					}
				}

				yield Main::getInstance()->getStd()->sleep(20 * 8);
			}
		});
	}


	/***
	 * @param string  $line
	 * @param Closure $closure returns [line number, line text]
	 */
	public static function addCallback(string $line, Closure $closure): void {
		self::$closure[$line] = $closure;
	}

	public static function setStartingScoreboard(Player $player, bool $delay = false): void {
		self::clearCache($player);
		ScoreFactory::setScore($player, "§r§e§lSKYBLOCK", SetDisplayObjectivePacket::SORT_ORDER_DESCENDING);

		Await::f2c(function() use($player) {
			self::setLine($player, self::LINE_PERSONAL_CLOSE, null);
			yield Main::getInstance()->getStd()->sleep(1);

			self::setLine($player, self::LINE_PERSONAL_XP, null);
			self::setLine($player, self::LINE_PERSONAL_ESSENCE, null);
			yield Main::getInstance()->getStd()->sleep(1);
			self::setLine($player, self::LINE_PERSONAL_MONEY, null);
			yield Main::getInstance()->getStd()->sleep(1);
			self::setLine($player, self::LINE_PERSONAL_TEXT, null);



			self::setLine($player, self::LINE_VOTE_GOAL, self::$lastVoteGoal);
			self::setLine($player, self::LINE_IP, null);
		});
	}

	public static function setLine(Player $player, string $line, $value, ?Session $session = null): void {
		if(!isset(self::$closure[$line])) return;
		if(!$player->isOnline()) return;

		assert($player instanceof AetherPlayer);

		$data = (self::$closure[$line])($player, ($session ?? $player->getCurrentProfilePlayerSession()), $value);
		$slot = $data[0];
		$text = $data[1];
		if(isset(self::$cache[$player->getName()][$slot])){
			self::$cache[$player->getName()][$slot] = $text;
			self::sendFromCache($player);
			return;
		}

		self::$cache[$player->getName()][$slot] = $text;
		ScoreFactory::setScoreLine($player, $data[0], $data[1]);
	}
	
	public static function removeLine(Player $player, Session $session, string $line, bool $update = true): void {
		$data = (self::$closure[$line])($player, $session, null);
		unset(self::$cache[$player->getName()][$data[0]]);

		if($update){
			self::sendFromCache($player);
		}
	}

	public static function sendFromCache(Player $player): void {
		if(!$player->isOnline()) return;

		if(isset(self::$cache[$player->getName()])){
			ScoreFactory::setScore($player, " " . Main::PLANET, SetDisplayObjectivePacket::SORT_ORDER_DESCENDING);
			$entries = [];
			$objective = ScoreFactory::getScore($player);
			foreach(self::$cache[$player->getName()] as $k => $v){
				$entry = new ScorePacketEntry();
				$entry->objectiveName = $objective;
				$entry->type = ScorePacketEntry::TYPE_FAKE_PLAYER;
				$entry->customName = $v;
				$entry->score = $k;
				$entry->scoreboardId = $k;
				$entries[] = $entry;
			}

			$pk = new SetScorePacket();
			$pk->type = $pk::TYPE_CHANGE;
			$pk->entries = $entries;
			$player->getNetworkSession()->sendDataPacket($pk);
		}
	}

	public static function clearCache(Player $player): void {
		unset(self::$cache[$player->getName()]);
	}
}