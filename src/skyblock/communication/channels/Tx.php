<?php


declare(strict_types=1);

namespace skyblock\communication\channels;

use pocketmine\snooze\SleeperNotifier;

/**
 * This class refers to the internal queue used to transmit data from a thread, to the main thread
 */
class Tx extends BaseChannel {
	/** @var SleeperNotifier */
	private $notifier;

	public function __construct(SleeperNotifier $notifier) {
		parent::__construct();
		$this->notifier = $notifier;
	}

	public function send(object | array $data) {
		$this->synchronized(function () use ($data) : void {
			parent::send($data);
			$this->notify();
			$this->notifier->wakeupSleeper();
		});
	}
}