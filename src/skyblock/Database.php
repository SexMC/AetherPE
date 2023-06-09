<?php

declare(strict_types=1);

namespace skyblock;

use pocketmine\timings\Timings;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use RedisClient\Client\Version\RedisClient6x0;
use RedisClient\ClientFactory;
use skyblock\utils\Queries;

class Database {

	/**
	 * @var RedisClient6x0
	 */
	private RedisClient6x0 $redis;

	private static Database $instance;

	private DataConnector $libasynql;

	private DataConnector $logs;

	public function __construct(){
		self::$instance = $this;
		if(PHP_OS === "WINNT"){
			$this->redis = ClientFactory::create([
				'server'   => '127.0.0.1:6379',
				'timeout'  => 2,
				'version'  => '6.0',
				//	'password' => 'A84418(ùµùµ:;(659))--=_ù^$-e)à}tH&&87;:,?ééOL684/*(à)rPE7438'
			]);
		} else {
			$this->redis = ClientFactory::create([
				'server'   => '135.148.150.31:6379',
				'timeout'  => 2,
				'version'  => '6.0',
				'password' => 'A84418(ùµùµ:;(659))--=_ù^$-e)à}tH&&87;:,?ééOL684/*(à)rPE7438'
			]);

		}

		$this->libasynql = libasynql::create(Main::getInstance(), Main::getInstance()->getConfig()->get("libasynql"), ["mysql" => ["mysql/kits.sql", "mysql/worlds.sql", "mysql/safezones.sql", "mysql/warps.sql", "mysql/bounty.sql"]]);

		$this->libasynql->executeGeneric(Queries::KITS_TABLE_CREATE);
		$this->libasynql->executeGeneric(Queries::WORLDS_TABLE_CREATE);
		$this->libasynql->executeGeneric(Queries::SAFEZONES_TABLE_CREATE);
		$this->libasynql->executeGeneric(Queries::WARP_TABLE_CREATE);

		$this->libasynql->executeGeneric(Queries::BOUNTY_HISTORY_TABLE_CREATE);
		$this->libasynql->executeGeneric(Queries::BOUNTY_TABLE_CREATE);

		$this->logs = libasynql::create(Main::getInstance(), Main::getInstance()->getConfig()->get("logs"), ["mysql" => ["mysql/logs.sql"]]);
	}

	/**
	 * @return DataConnector
	 */
	public function getLogs() : DataConnector{
		return $this->logs;
	}

	/**
	 * @return Database
	 */
	public static function getInstance() : Database{
		return self::$instance;
	}

	/**
	 * @return RedisClient6x0
	 */
	public function getRedis(): RedisClient6x0{
		return $this->redis;
	}

	public function redisGet(string $get) {
		Timings::$tickTileEntity->startTiming();
		$start = microtime(true);
		//echo "Before redis GET: $get" . "\n";
		$resp = $this->redis->get($get);
		$end = microtime(true);
		Timings::$tickTileEntity->stopTiming();

		//echo "Took: " . ($end - $start) . "s for redis GET query: $get" . "\n";

		return $resp;
	}

	public function redisSet(string $get, $value) {
		Timings::$tickTileEntity->startTiming();
		$start = microtime(true);
		//echo "Before redis SET: $get";
		$resp = $this->redis->set($get, $value);
		$end = microtime(true);
		Timings::$tickTileEntity->stopTiming();

		//echo "Took: " . ($end - $start) . "s for redis SET query: $get" . "\n";

		return $resp;
	}

	/**
	 * @return DataConnector
	 */
	public function getLibasynql() : DataConnector{
		return $this->libasynql;
	}

}