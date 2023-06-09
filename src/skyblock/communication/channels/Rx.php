<?php


declare(strict_types=1);

namespace skyblock\communication\channels;


use skyblock\communication\operations\BaseOperation;

/**
 * This class refers to the internal queue used to receive data from the main thread
 */
class Rx extends BaseChannel {

	public function send(object | array $data) {
		$this->synchronized(function () use ($data): void {
			parent::send($data);
			$this->notify();
		});
	}
}