<?php

declare(strict_types=1);

namespace skyblock\logs;

use skyblock\Database;
use skyblock\logs\types\SellLog;
use skyblock\traits\AetherSingletonTrait;
use skyblock\utils\Queries;
use Threaded;

class LogHandler {
	use AetherSingletonTrait;

	private Threaded $rx;

	private LogThread $thread;

	public function __construct(){
		self::setInstance($this);

		$this->rx = new Threaded();
		$this->thread = new LogThread($this->rx);
		$this->thread->start(PTHREADS_INHERIT_NONE);
	}

	public function log(Log $log): void {
		if($log instanceof SellLog){
			Database::getInstance()->getLogs()->executeInsert(Queries::SELL_LOG, ["player" => $log->data["player"], "unix" => time(), "items" => json_encode($log->data["items"])]);
		}

		$this->rx[] = igbinary_serialize($log);
	}
}