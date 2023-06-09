<?php

declare(strict_types=1);

namespace skyblock\logs;

use skyblock\communication\http\HttpPostRequest;
use skyblock\logs\types\ChatLog;
use skyblock\logs\types\SlotsLog;

abstract class Log implements ILog{

	public array $data = [];

	public array $extraData = [];

	public function execute(): void {
		$this->data["time"] = date('H:i:s d/m/Y');

		$type = $this->getType();

		$req = new HttpPostRequest("logs/$type", $this->data);
		$req->setBaseURL("http://127.0.0.1:8080/");


		$req->execute();
	}

	public abstract function getType(): string;

	public function getExtraRoute(): string {
		return "";
	}

	public function getExtraData(): array {
		return $this->extraData;
	}
}