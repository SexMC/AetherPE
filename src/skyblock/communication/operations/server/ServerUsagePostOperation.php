<?php

declare(strict_types=1);

namespace skyblock\communication\operations\server;

use skyblock\communication\http\HttpPostRequest;
use skyblock\communication\operations\BaseOperation;

class ServerUsagePostOperation extends BaseOperation {


	public function __construct(
		private string $server,
		private float $cpu,
		private float $tps,
		private int $pCount,
		private int $isCount)
	{
	}

	public function execute() : array{
		$req =  (new HttpPostRequest("status?server={$this->server}&cpu={$this->cpu}&tps={$this->tps}&pcount={$this->pCount}&iscount={$this->isCount}", $this->data));

		$req->setBaseURL("http://135.148.150.31:9038/");
		return $req->execute();
	}
}