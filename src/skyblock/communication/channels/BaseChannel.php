<?php

declare(strict_types=1);

namespace skyblock\communication\channels;

use Threaded;

abstract class BaseChannel extends Threaded {
	/** @var Threaded */
	protected $queue;
	/** @var bool */
	private $valid = true;

	public function __construct() {
		$this->queue = new Threaded();
	}

	public function send(object | array $data) {
		$this->queue[] = igbinary_serialize($data);
	}

	public function receive(object | array &$data): bool {
		$row = $this->queue->shift();
		if(is_string($row)) {
			$data = igbinary_unserialize($row);
			return true;
		}
		return false;
	}

	public function invalidate(): void {
		$this->synchronized(function (): void {
			$this->valid = false;
			$this->notify();
		});
	}

	/**
	 * @return bool
	 */
	public function isValid(): bool {
		return $this->valid;
	}

	public function clear(): void {
		$this->synchronized(function (): void {
			while($this->queue->count() > 0){
				$this->queue->shift();
			}
			$this->notify();
		});
	}
}